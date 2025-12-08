<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../app/Models/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';


use App\Models\Database;
use App\Controllers\AuthController;

$pdo = Database::connect();
$auth = new AuthController($pdo);

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($auth->login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $err = 'Invalid username or password';
    }
}
?>
<?php include __DIR__ . '/../app/Views/header.php'; ?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div style="background: #e3f0ff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); padding: 32px 28px; margin-top: 32px;">
      <h3 style="color: #2563eb; font-weight: 700;">Login</h3>
      <?php if($err): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input class="form-control" name="username" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input class="form-control" name="password" type="password" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
      </form>
      <div class="text-center mt-3">
        <p>Don’t have an account? <a href="register.php" class="btn btn-outline-secondary btn-sm">Register here</a></p>
      </div>
    </div>
  </div>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>
