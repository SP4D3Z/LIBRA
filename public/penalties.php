<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;
use App\Controllers\PenaltyController;

require_login();

$pdo = Database::connect();
$controller = new PenaltyController($pdo);
$currentUser = user();

$controller->handleMarkPaid($_GET, $currentUser);
$penalties = $controller->getPenalties();

include VIEWS_PATH . '/header.php';
?>
<div class="row">
  <div class="col-md-12">
    <h3>Penalties</h3>
    <table class="table table-bordered">
      <thead><tr><th>User</th><th>Type</th><th>Amount</th><th>Paid</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach($penalties as $p): ?>
          <tr>
            <td><?php echo htmlspecialchars($p['first_name'].' '.$p['last_name']); ?></td>
            <td><?php echo htmlspecialchars($p['penalty_type']); ?></td>
            <td>₱<?php echo number_format($p['amount'],2); ?></td>
            <td><?php echo $p['is_paid'] ? 'Yes' : 'No'; ?></td>
            <td>
              <?php if(!$p['is_paid']): ?>
                <a class="btn btn-sm btn-success" href="penalties.php?pay=<?php echo $p['penalty_id']; ?>">Mark Paid</a>
              <?php else: echo '-'; endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>