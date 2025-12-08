<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../app/Models/Database.php';
require_once __DIR__ . '/../app/Models/BorrowModel.php';
require_once __DIR__ . '/../app/Controllers/BorrowController.php';
include VIEWS_PATH . '/header.php';
require_login();

use App\Models\Database;
use App\Controllers\BorrowController;

$pdo = Database::connect();
$controller = new BorrowController($pdo);
$currentUser = user();

$controller->handleReturn($_GET, $currentUser);

list($err, $msg) = $controller->handleBorrow($_POST, $currentUser);

$borrows = $controller->getBorrowings();
$allBooks = $controller->getBooks();
?>
<div class="row">
  <div class="col-md-8">
    <h3>Borrowing Transactions</h3>
    <?php if(!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <?php if(!empty($err)): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>

    <?php
      // prepare variables for the BorrowTable view
      // BorrowTable.php expects $borrows
      include __DIR__ . '/../app/Views/BorrowTable.php';
    ?>
  </div>

  <div class="col-md-4">
    <h4>Borrow Book</h4>
    <?php
      // prepare variables for the borrow form view
      $user = $currentUser;
      $books = $allBooks;
      include __DIR__ . '/../app/Views/borrow_form.php';
    ?>
  </div>
</div>
<?php
include VIEWS_PATH . '/footer.php';
?>
