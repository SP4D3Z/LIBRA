<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../app/Models/Database.php';
require_once __DIR__ . '/../app/Models/ReservationModel.php';
require_once __DIR__ . '/../app/Controllers/ReservationController.php';
include VIEWS_PATH . '/header.php';
require_login();

use App\Models\Database;
use App\Controllers\ReservationController;

$pdo = Database::connect();
$controller = new ReservationController($pdo);

$msg = $controller->handleReservation($_POST);
$reservations = $controller->getReservations();
$users = $controller->getUsers();
$books = $controller->getBooks();
?>
<div class="row">
  <div class="col-md-8">
    <h3>Reservations</h3>
    <?php if(!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <table class="table table-bordered">
      <thead><tr><th>User</th><th>Book</th><th>Reserved</th><th>Expiry</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach($reservations as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['first_name'].' '.$r['last_name']); ?></td>
            <td><?php echo htmlspecialchars($r['title']); ?></td>
            <td><?php echo $r['reservation_date']; ?></td>
            <td><?php echo $r['expiry_date']; ?></td>
            <td><?php echo $r['status']; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="col-md-4">
    <h4>Reserve Book</h4>
    <form method="post">
      <div class="mb-2">
        <select name="user_id" class="form-control" required>
          <?php foreach($users as $u): ?>
            <option value="<?php echo $u['user_id']; ?>"><?php echo htmlspecialchars($u['first_name'].' '.$u['last_name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-2">
        <select name="book_id" class="form-control" required>
          <?php foreach($books as $b): ?>
            <option value="<?php echo $b['book_id']; ?>"><?php echo htmlspecialchars($b['title']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn btn-primary" name="reserve">Reserve</button>
    </form>
  </div>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>
