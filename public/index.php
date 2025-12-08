<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../app/Models/Database.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
include VIEWS_PATH . '/header.php';
require_login();

use App\Models\Database;
use App\Controllers\DashboardController;

$pdo = Database::connect();
$controller = new DashboardController($pdo);
$u = $controller->getCurrentUser();
?>

<div class="row">
  <div class="col-md-12">
    <h2>Welcome, <?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?>!</h2>
    <p>Role: <strong><?php echo htmlspecialchars($u['user_type']); ?></strong></p>
    <div class="list-group">
      <?php if ($u['user_type'] === 'librarian' || $u['user_type'] === 'staff'): ?>
        <a href="books.php" class="list-group-item list-group-item-action">Manage Books</a>
      <?php endif; ?>
      <a href="borrow.php" class="list-group-item list-group-item-action">Borrow / Return</a>
      <a href="reservations.php" class="list-group-item list-group-item-action">Reservations</a>
      <a href="penalties.php" class="list-group-item list-group-item-action">Penalties</a>
      <?php if ($u['user_type'] === 'librarian' || $u['user_type'] === 'staff'): ?>
        <a href="users.php" class="list-group-item list-group-item-action">Users</a>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>