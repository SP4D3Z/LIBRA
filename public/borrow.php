<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;
use App\Controllers\BorrowController;
use App\Models\BorrowModel;

require_login();

$pdo = Database::connect();
$controller = new BorrowController($pdo);
$currentUser = user();

$controller->handleReturn($_GET, $currentUser);
list($err, $msg) = $controller->handleBorrow($_POST, $currentUser);

// Get borrowings based on user role
if (in_array($currentUser['user_type'], ['staff', 'librarian'])) {
    // Staff/Librarian: See all transactions
    $borrows = $controller->getBorrowings();
    $pageTitle = "All Borrowing Transactions";
} else {
    // Student/Teacher: See only their own transactions
    $borrows = $controller->listUserBorrows();
    $pageTitle = "My Borrowings";
}

$allBooks = $controller->getBooks();

// Get all users for staff/librarians
if (in_array($currentUser['user_type'], ['staff', 'librarian'])) {
    $allUsers = $controller->getUsers();
} else {
    $allUsers = null;
}
$studentSummary = null;
if($currentUser['user_type'] === 'student') {
    $borrowModel = new BorrowModel($pdo);
    $semester = $borrowModel->getCurrentSemester();
    $borrowed_count = $borrowModel->getStudentBorrowCount($currentUser['user_id'], $semester['start'], $semester['end']);
    $studentSummary = [
        'semester' => $semester,
        'borrowed' => $borrowed_count,
        'remaining' => 3 - $borrowed_count
    ];
}

include VIEWS_PATH . '/header.php';
?>
<!-- Rest of your HTML code remains the same -->
<div class="row">
  <div class="col-md-8">
    <h3>
        <i class="bi bi-book"></i> <?php echo $pageTitle; ?>
        <?php if (!in_array($currentUser['user_type'], ['staff', 'librarian'])): ?>
            <small class="text-muted">(Your borrowings only)</small>
        <?php endif; ?>
    </h3>
    
    <?php if(!empty($msg)): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>
    
    <?php if(!empty($err)): ?>
        <div class="alert alert-danger"><?php echo $err; ?></div>
    <?php endif; ?>

    <?php if(empty($borrows)): ?>
        <div class="card">
            <div class="card-body text-center text-muted">
                <i class="bi bi-book" style="font-size: 3rem;"></i>
                <h5>No borrowing records found</h5>
                <p>
                    <?php if (!in_array($currentUser['user_type'], ['staff', 'librarian'])): ?>
                        You haven't borrowed any books yet.
                    <?php else: ?>
                        No borrowing transactions in the system.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <?php if (in_array($currentUser['user_type'], ['staff', 'librarian'])): ?>
                            <th>Borrower</th>
                        <?php endif; ?>
                        <th>Book Title</th>
                        <th>Borrowed Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($borrows as $row): 
                        $is_overdue = isset($row['due_date']) && strtotime($row['due_date']) < time() && $row['status'] === 'active';
                        $is_returned = $row['status'] === 'returned';
                    ?>
                        <tr class="<?php echo $is_overdue ? 'table-warning' : ($is_returned ? 'table-success' : ''); ?>">
                            <?php if (in_array($currentUser['user_type'], ['staff', 'librarian'])): ?>
                                <td>
                                    <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                    <br>
                                    <small class="text-muted">(<?php echo $row['user_type']; ?>)</small>
                                </td>
                            <?php endif; ?>
                            
                            <td>
                                <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                <?php if (isset($row['author'])): ?>
                                    <br><small class="text-muted">by <?php echo htmlspecialchars($row['author']); ?></small>
                                <?php endif; ?>
                            </td>
                            
                            <td><?php echo $row['borrowed_date']; ?></td>
                            
                            <td class="<?php echo $is_overdue ? 'text-danger fw-bold' : ''; ?>">
                                <?php echo $row['due_date']; ?>
                                <?php if ($is_overdue): ?>
                                    <br>
                                    <small class="text-danger">
                                        <i class="bi bi-exclamation-triangle"></i> Overdue
                                    </small>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php if ($row['status'] === 'active'): ?>
                                    <?php if ($is_overdue): ?>
                                        <span class="badge bg-danger">Overdue</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Active</span>
                                    <?php endif; ?>
                                <?php elseif ($row['status'] === 'returned'): ?>
                                    <span class="badge bg-success">Returned</span>
                                    <?php if (isset($row['returned_date'])): ?>
                                        <br><small>on <?php echo $row['returned_date']; ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?php echo ucfirst($row['status']); ?></span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php if($row['status'] === 'active'): ?>
                                    <?php if (in_array($currentUser['user_type'], ['staff', 'librarian'])): ?>
                                        <!-- Staff can return any book -->
                                        <a class="btn btn-sm btn-primary" 
                                           href="borrow.php?return=1&id=<?php echo $row['transaction_id']; ?>"
                                           onclick="return confirm('Mark book as returned?')">
                                            <i class="bi bi-check-circle"></i> Mark Returned
                                        </a>
                                    <?php else: ?>
                                        <!-- Students/teachers can only see return option if they're the borrower -->
                                        <?php if ($row['user_id'] == $currentUser['user_id']): ?>
                                            <a class="btn btn-sm btn-primary" 
                                               href="borrow.php?return=1&id=<?php echo $row['transaction_id']; ?>"
                                               onclick="return confirm('Are you returning this book?')">
                                                <i class="bi bi-arrow-return-left"></i> Return Book
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
  </div>

  <div class="col-md-4">
    <?php if (in_array($currentUser['user_type'], ['staff', 'librarian'])): ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-book-plus"></i> Borrow Book for User</h5>
            </div>
            <div class="card-body">
                <?php
                    $user = $currentUser;
                    $books = $allBooks;
                    include VIEWS_PATH . '/borrow_form.php';
                ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Student/Teacher: Show borrowing form for themselves -->
        <?php if (!empty($allBooks)): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-book-plus"></i> Borrow a Book</h5>
                </div>
                <div class="card-body">
                    <?php
                        $user = $currentUser;
                        $books = $allBooks;
                        include VIEWS_PATH . '/borrow_form.php';
                    ?>
                </div>
                <div class="card-footer">
                    <?php if($currentUser['user_type'] === 'student'): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i>
                            <small>
                                <strong>Student Limit:</strong> You can borrow up to 3 books per semester.
                                Overdue books and unpaid penalties will block new borrowings.
                            </small>
                        </div>
                    <?php elseif($currentUser['user_type'] === 'teacher'): ?>
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i>
                            <small>
                                <strong>Teacher Note:</strong> Unlimited borrowing, but all books must be 
                                returned at semester end for clearance.
                            </small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center text-muted">
                    <i class="bi bi-book" style="font-size: 2rem;"></i>
                    <h6>No Books Available</h6>
                    <p class="mb-0">There are currently no books available for borrowing.</p>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Student borrowing summary -->
        <?php if($currentUser['user_type'] === 'student'): ?>
            <?php
                // Get student's semester borrowing count
                $borrowModel = new \App\Models\BorrowModel($pdo);
                $semester = $borrowModel->getCurrentSemester();
                $borrowed_count = $borrowModel->getStudentBorrowCount($currentUser['user_id'], $semester['start'], $semester['end']);
                $remaining = 3 - $borrowed_count;
            ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-graph-up"></i> Semester Summary</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong><?php echo $semester['name']; ?></strong><br>
                        Books borrowed: <strong><?php echo $borrowed_count; ?>/3</strong>
                    </p>
                    <div class="progress mb-3">
                        <?php 
                        $percentage = ($borrowed_count / 3) * 100;
                        $color = $borrowed_count >= 3 ? 'danger' : ($borrowed_count >= 2 ? 'warning' : 'success');
                        ?>
                        <div class="progress-bar bg-<?php echo $color; ?>" 
                             role="progressbar" 
                             style="width: <?php echo min($percentage, 100); ?>%">
                            <?php echo $borrowed_count; ?>/3
                        </div>
                    </div>
                    <?php if($borrowed_count >= 3): ?>
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle"></i>
                            You have reached your semester borrowing limit.
                        </div>
                    <?php else: ?>
                        <p class="mb-0 text-success">
                            <i class="bi bi-check-circle"></i>
                            You can still borrow <?php echo $remaining; ?> more book<?php echo $remaining !== 1 ? 's' : ''; ?> this semester.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>