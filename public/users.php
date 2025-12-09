<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;
use App\Controllers\UserController;

require_login();

$pdo = Database::connect();
$controller = new UserController($pdo);

// Only librarians/staff can add users
$canAdd = in_array(user()['user_type'], ['librarian','staff']);

// Handle create user
list($msg, $err) = $controller->handleCreateUser($_POST, $canAdd);

// Handle delete user
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    list($deleteMsg, $deleteErr) = $controller->handleDeleteUser((int)$_GET['delete'], user());
    if ($deleteMsg) $msg = $deleteMsg;
    if ($deleteErr) $err = $deleteErr;
    
    // Redirect to avoid resubmission
    header('Location: users.php?msg=' . urlencode($msg ?? '') . '&err=' . urlencode($err ?? ''));
    exit;
}

// Handle messages from redirect
if (isset($_GET['msg'])) $msg = $_GET['msg'];
if (isset($_GET['err'])) $err = $_GET['err'];

// fetch users
$users = $controller->getUsers();

include VIEWS_PATH . '/header.php';
?>
<div class="row">
  <div class="col-md-8">
    <h3>Users</h3>
    <?php if(!empty($msg)): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <?php if(!empty($err)): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
    
    <script>
    function confirmDelete(userId, userName) {
        if (confirm('Are you sure you want to deactivate user: ' + userName + '?')) {
            window.location.href = 'users.php?delete=' + userId;
        }
    }
    </script>
    
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Name</th>
          <th>Type</th>
          <th>Active</th>
          <th>Created</th>
          <?php if(in_array(user()['user_type'], ['librarian', 'staff'])): ?>
          <th>Actions</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($users as $r): ?>
          <tr>
            <td><?php echo $r['user_id']; ?></td>
            <td><?php echo htmlspecialchars($r['username']); ?></td>
            <td><?php echo htmlspecialchars($r['first_name'].' '.$r['last_name']); ?></td>
            <td><?php echo htmlspecialchars($r['user_type']); ?></td>
            <td><?php echo $r['is_active'] ? 'Yes' : 'No'; ?></td>
            <td><?php echo $r['created_at']; ?></td>
            <?php if(in_array(user()['user_type'], ['librarian', 'staff'])): ?>
            <td>
              <button class="btn btn-sm btn-danger" 
                      onclick="confirmDelete(<?php echo $r['user_id']; ?>, '<?php echo htmlspecialchars($r['username']); ?>')"
                      <?php echo $r['user_id'] == user()['user_id'] ? 'disabled' : ''; ?>>
                Deactivate
              </button>
              <?php if($r['user_id'] == user()['user_id']): ?>
                <small class="text-muted">(Your account)</small>
              <?php endif; ?>
            </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php if($canAdd): ?>
  <div class="col-md-4">
    <h4>Add User</h4>
    <form method="post">
      <div class="mb-2"><input class="form-control" name="username" placeholder="Username" required></div>
      <div class="mb-2"><input class="form-control" name="first_name" placeholder="First Name" required></div>
      <div class="mb-2"><input class="form-control" name="last_name" placeholder="Last Name" required></div>
      <div class="mb-2"><input class="form-control" name="phone" placeholder="Phone Number"></div>
      <div class="mb-2"><input class="form-control" name="address" placeholder="Address"></div>
      <div class="mb-2"><input class="form-control" name="password" placeholder="Password" type="password" required></div>
      <div class="mb-2">
        <select name="user_type" class="form-control">
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
          <option value="librarian">Librarian</option>
          <option value="staff">Staff</option>
        </select>
      </div>
      <button class="btn btn-success">Create</button>
    </form>
  </div>
  <?php endif; ?>
</div>
<?php include VIEWS_PATH . '/footer.php'; ?>