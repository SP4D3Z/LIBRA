<?php
require_once __DIR__ . '/config.php';

use App\Models\Database;
use App\Controllers\RegisterController;

if(isset($_SESSION['user'])) header('Location: index.php');

$pdo = Database::connect();
$controller = new RegisterController($pdo);

list($err, $msg) = $controller->handleRegister($_POST);

include VIEWS_PATH . '/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <h3>Register Account</h3>
    <?php if($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>
    <?php if($err): ?><div class="alert alert-danger"><?php echo $err; ?></div><?php endif; ?>
   <form method="post">
    <div class="mb-2">
        <input class="form-control" name="username" placeholder="Username" required>
    </div>
    
    <div class="mb-2">
        <input class="form-control" name="first_name" placeholder="First Name" required>
    </div>
    <div class="mb-2">
        <input class="form-control" name="last_name" placeholder="Last Name" required>
    </div>
    <div class="mb-2">
        <input class="form-control" name="phone" placeholder="Phone Number" required>
    </div>
    <div class="mb-2">
        <input class="form-control" name="address" placeholder="Address" required>
    </div>
    <div class="mb-2">
        <input class="form-control" type="password" name="password" placeholder="Password" required>
    </div>
    <div class="mb-2">
        <input class="form-control" type="password" name="confirm_password" placeholder="Confirm Password" required>
    </div>
    <div class="mb-2">
        <label>User Type</label>
        <select class="form-control" name="user_type" id="user_type" required onchange="toggleExtraFields()">
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="staff">Staff</option>
            <option value="librarian">Librarian</option>
        </select>
    </div>
      <div id="student_fields" style="display:none;">
        <div class="mb-2"><input class="form-control" name="program" placeholder="Program (e.g. BSIT)"></div>
        <div class="mb-2"><input class="form-control" name="year_level" placeholder="Year Level (e.g. 1,2,3,4)"></div>
      </div>
      <div id="teacher_staff_fields" style="display:none;">
        <div class="mb-2"><input class="form-control" name="department" placeholder="Department"></div>
        <div class="mb-2"><input class="form-control" name="position" placeholder="Position"></div>
      </div>
      <div id="librarian_fields" style="display:none;">
        <div class="mb-2"><input class="form-control" name="department" placeholder="Department (default Library)"></div>
      </div>
      <button class="btn btn-primary">Register</button>
    </form>
  </div>
</div>
<script>
function toggleExtraFields(){
  var type = document.getElementById('user_type').value;
  document.getElementById('student_fields').style.display = (type==='student')?'block':'none';
  document.getElementById('teacher_staff_fields').style.display = (type==='teacher'||type==='staff')?'block':'none';
  document.getElementById('librarian_fields').style.display = (type==='librarian')?'block':'none';
}
toggleExtraFields();
</script>
<?php include VIEWS_PATH . '/footer.php'; ?>