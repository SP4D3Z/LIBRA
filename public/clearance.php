<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;
use App\Controllers\ClearanceController;

require_login();

$pdo = Database::connect();
$controller = new ClearanceController($pdo);
$currentUser = user();

// Only staff and librarians can access clearance page
if (!in_array($currentUser['user_type'], ['staff', 'librarian'])) {
    header('Location: index.php');
    exit;
}

$msg = '';
$err = '';

// Handle clearance request for a specific user
if (isset($_POST['process_clearance']) && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    $result = $controller->processClearance($user_id, $currentUser['user_id']);
    
    if ($result['success']) {
        $msg = $result['message'];
    } else {
        $err = $result['message'];
    }
}

// Check status for a specific user
$clearanceStatus = null;
$selectedUser = null;
if (isset($_GET['check_user']) && is_numeric($_GET['check_user'])) {
    $selectedUserId = (int)$_GET['check_user'];
    $clearanceStatus = $controller->checkClearanceStatus($selectedUserId);
    $selectedUser = $clearanceStatus['user_info'];
}

// Get all users who might need clearance
$users = $controller->getUsersForClearance();

include VIEWS_PATH . '/header.php';
?>
<div class="row">
    <div class="col-md-12">
        <h3><i class="bi bi-check-circle"></i> Student & Teacher Clearance</h3>
        
        <?php if($msg): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>
        
        <?php if($err): ?>
            <div class="alert alert-danger"><?php echo $err; ?></div>
        <?php endif; ?>

        <div class="row">
            <!-- User Selection Section -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Select User</h5>
                    </div>
                    <div class="card-body">
                        <form method="get" action="clearance.php">
                            <div class="mb-3">
                                <label class="form-label">Select User to Check Clearance:</label>
                                <select name="check_user" class="form-control" required onchange="this.form.submit()">
                                    <option value="">-- Select User --</option>
                                    <?php foreach($users as $user): ?>
                                        <option value="<?php echo $user['user_id']; ?>" 
                                            <?php echo isset($selectedUser) && $selectedUser['username'] == $user['username'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['user_type'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Clearance Status Section -->
            <div class="col-md-8">
                <?php if($clearanceStatus && $selectedUser): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                Clearance Status: <?php echo htmlspecialchars($selectedUser['name']); ?>
                                <small class="text-muted">(<?php echo $selectedUser['type']; ?>)</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if($clearanceStatus['eligible']): ?>
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle-fill"></i> 
                                    <strong>Eligible for Clearance</strong>
                                    <p class="mb-0 mt-2">This user has no active borrowings or unpaid penalties.</p>
                                </div>
                                
                                <form method="post">
                                    <input type="hidden" name="user_id" value="<?php echo $selectedUserId; ?>">
                                    <button type="submit" name="process_clearance" class="btn btn-success">
                                        <i class="bi bi-check-lg"></i> Approve Clearance
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <strong>Not Eligible for Clearance</strong>
                                    <ul class="mt-2 mb-3">
                                        <?php foreach($clearanceStatus['issues'] as $issue): ?>
                                            <li><?php echo $issue; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    
                                    <?php if(isset($clearanceStatus['active_borrowings'])): ?>
                                        <h6>Active Borrowings:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Book Title</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($clearanceStatus['active_borrowings'] as $borrowing): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($borrowing['title']); ?></td>
                                                            <td class="<?php echo strtotime($borrowing['due_date']) < time() ? 'text-danger' : ''; ?>">
                                                                <?php echo $borrowing['due_date']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo strtotime($borrowing['due_date']) < time() ? 'Overdue' : 'Active'; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="borrow.php" class="btn btn-primary">View Borrowing Records</a>
                                    <a href="penalties.php" class="btn btn-outline-primary">View Penalties</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center text-muted">
                            <i class="bi bi-person-check" style="font-size: 3rem;"></i>
                            <h5>Select a user to check clearance status</h5>
                            <p>Choose a student or teacher from the dropdown to view their clearance eligibility.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>