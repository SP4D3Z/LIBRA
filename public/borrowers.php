<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;
use App\Controllers\BorrowerController;

require_login();

$pdo = Database::connect();
$controller = new BorrowerController($pdo);
$currentUser = user();

// Only staff and librarians can access
if (!in_array($currentUser['user_type'], ['staff', 'librarian'])) {
    header('Location: index.php');
    exit;
}

$borrowers = $controller->getBorrowerStatus();
$selectedBorrower = null;

if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $selectedBorrower = $controller->getBorrowerDetails((int)$_GET['user_id']);
}

include VIEWS_PATH . '/header.php';
?>
<div class="row">
    <div class="col-md-12">
        <h3><i class="bi bi-people"></i> Borrower Management</h3>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">All Borrowers Status</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Active Borrowings</th>
                                <th>Overdue Books</th>
                                <th>Unpaid Penalties</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($borrowers)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No borrowers found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($borrowers as $borrower): ?>
                                    <tr class="<?php echo $borrower['overdue_books'] > 0 ? 'table-warning' : ''; ?>">
                                        <td><?php echo htmlspecialchars($borrower['first_name'] . ' ' . $borrower['last_name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $borrower['user_type'] === 'student' ? 'info' : 'primary'; ?>">
                                                <?php echo ucfirst($borrower['user_type']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-<?php echo $borrower['active_borrowings'] > 0 ? 'warning' : 'secondary'; ?>">
                                                <?php echo $borrower['active_borrowings']; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if($borrower['overdue_books'] > 0): ?>
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-exclamation-triangle"></i> <?php echo $borrower['overdue_books']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($borrower['unpaid_penalties'] > 0): ?>
                                                <span class="badge bg-danger">
                                                    ₱<?php echo number_format($borrower['unpaid_penalties'], 2); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">₱0.00</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="borrowers.php?user_id=<?php echo $borrower['user_id']; ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> View Details
                                            </a>
                                            <a href="clearance.php?check_user=<?php echo $borrower['user_id']; ?>" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle"></i> Check Clearance
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <?php if($selectedBorrower): ?>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        Borrower Details: <?php echo htmlspecialchars($selectedBorrower['user']['first_name'] . ' ' . $selectedBorrower['user']['last_name']); ?>
                        <span class="badge bg-light text-dark"><?php echo ucfirst($selectedBorrower['user']['user_type']); ?></span>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Borrower details would go here -->
                    <p>Total Books Borrowed: <?php echo $selectedBorrower['user']['total_borrowed']; ?></p>
                    <p>Books Returned: <?php echo $selectedBorrower['user']['returned_count']; ?></p>
                    <p>Active Reservations: <?php echo $selectedBorrower['user']['total_reservations']; ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>