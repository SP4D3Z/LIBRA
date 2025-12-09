<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;
use App\Controllers\ClearanceController;

require_login();

$pdo = Database::connect();
$controller = new ClearanceController($pdo);
$currentUser = user();

// Only students and teachers can access their own clearance status
if (!in_array($currentUser['user_type'], ['student', 'teacher'])) {
    header('Location: index.php');
    exit;
}

// Check current user's clearance status
$clearanceStatus = $controller->checkClearanceStatus($currentUser['user_id']);

include VIEWS_PATH . '/header.php';
?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-clipboard-check"></i> My Clearance Status
                </h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="display-1">
                        <?php if($clearanceStatus['eligible']): ?>
                            <i class="bi bi-check-circle-fill text-success"></i>
                        <?php else: ?>
                            <i class="bi bi-x-circle-fill text-danger"></i>
                        <?php endif; ?>
                    </div>
                    <h3>
                        <?php if($clearanceStatus['eligible']): ?>
                            <span class="text-success">CLEARED FOR SEMESTER END</span>
                        <?php else: ?>
                            <span class="text-danger">NOT CLEARED</span>
                        <?php endif; ?>
                    </h3>
                    <p class="text-muted">
                        <?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?>
                        <span class="badge bg-info"><?php echo ucfirst($currentUser['user_type']); ?></span>
                    </p>
                </div>
                
                <?php if($clearanceStatus['eligible']): ?>
                    <div class="alert alert-success">
                        <h5><i class="bi bi-check-circle"></i> Congratulations!</h5>
                        <p class="mb-0">You are eligible for semester clearance. You have no outstanding obligations.</p>
                    </div>
                    
                    <div class="text-center mt-4">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Next Steps:</strong> Visit the library staff to complete your clearance process.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <h5><i class="bi bi-exclamation-triangle"></i> Clearance Issues Found</h5>
                        <p>The following issues must be resolved before you can be cleared:</p>
                        <ul>
                            <?php foreach($clearanceStatus['issues'] as $issue): ?>
                                <li><?php echo $issue; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <?php if(isset($clearanceStatus['active_borrowings'])): ?>
                        <div class="mt-4">
                            <h5><i class="bi bi-book"></i> Active Borrowings</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Book Title</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($clearanceStatus['active_borrowings'] as $borrowing): 
                                            $is_overdue = strtotime($borrowing['due_date']) < time();
                                        ?>
                                            <tr class="<?php echo $is_overdue ? 'table-danger' : ''; ?>">
                                                <td><?php echo htmlspecialchars($borrowing['title']); ?></td>
                                                <td class="<?php echo $is_overdue ? 'text-danger fw-bold' : ''; ?>">
                                                    <?php echo $borrowing['due_date']; ?>
                                                    <?php if($is_overdue): ?>
                                                        <br><small class="text-danger">Overdue</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($is_overdue): ?>
                                                        <span class="badge bg-danger">Overdue</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">Active</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="borrow.php" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-arrow-return-left"></i> Return
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($clearanceStatus['unpaid_penalties']) && $clearanceStatus['unpaid_penalties'] > 0): ?>
                        <div class="alert alert-danger mt-4">
                            <h5><i class="bi bi-cash-coin"></i> Unpaid Penalties</h5>
                            <p class="mb-2">Total amount due: <strong>₱<?php echo number_format($clearanceStatus['unpaid_penalties'], 2); ?></strong></p>
                            <a href="penalties.php" class="btn btn-danger">
                                <i class="bi bi-credit-card"></i> Pay Penalties
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6><i class="bi bi-info-circle"></i> How to Resolve:</h6>
                        <ol class="mb-0">
                            <li>Return all borrowed books at the library counter</li>
                            <li>Pay any outstanding penalties at the library office</li>
                            <li>Visit library staff to verify your clearance status</li>
                        </ol>
                    </div>
                <?php endif; ?>
                
                <!-- Student-specific info -->
                <?php if($currentUser['user_type'] === 'student' && isset($clearanceStatus['semester_info'])): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Semester Borrowing Summary</h6>
                        </div>
                        <div class="card-body">
                            <p>
                                You have borrowed <strong><?php echo $clearanceStatus['semester_info']['borrowed']; ?></strong> 
                                out of <strong><?php echo $clearanceStatus['semester_info']['limit']; ?></strong> 
                                books allowed for <?php echo $clearanceStatus['semester_info']['semester']; ?>.
                            </p>
                            <div class="progress mb-3">
                                <?php 
                                $percentage = ($clearanceStatus['semester_info']['borrowed'] / $clearanceStatus['semester_info']['limit']) * 100;
                                $color = $percentage >= 100 ? 'danger' : ($percentage >= 80 ? 'warning' : 'success');
                                ?>
                                <div class="progress-bar bg-<?php echo $color; ?>" 
                                     role="progressbar" 
                                     style="width: <?php echo min($percentage, 100); ?>%">
                                    <?php echo $clearanceStatus['semester_info']['borrowed']; ?> / <?php echo $clearanceStatus['semester_info']['limit']; ?>
                                </div>
                            </div>
                            <?php if($clearanceStatus['semester_info']['borrowed'] >= $clearanceStatus['semester_info']['limit']): ?>
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    You have reached your semester borrowing limit.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Teacher-specific info -->
                <?php if($currentUser['user_type'] === 'teacher' && !empty($clearanceStatus['active_borrowings'])): ?>
                    <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle"></i>
                        <strong>Teacher Note:</strong> Teachers must return all borrowed books at the end of each semester 
                        to complete clearance, even if they are not overdue.
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="borrow.php" class="btn btn-outline-primary">
                        <i class="bi bi-book"></i> View My Borrowings
                    </a>
                    <a href="penalties.php" class="btn btn-outline-danger">
                        <i class="bi bi-cash-coin"></i> View My Penalties
                    </a>
                </div>
            </div>
            <div class="card-footer text-muted">
                Last checked: <?php echo date('F j, Y, g:i a'); ?>
            </div>
        </div>
    </div>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>