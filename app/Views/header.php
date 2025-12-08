
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Smart Library</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
body {
  background: linear-gradient(135deg, #e0e7ff 0%, #f0fdfa 100%);
  min-height: 100vh;
  font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}
.navbar {
  background: linear-gradient(90deg, #2563eb 60%, #38bdf8 100%);
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.navbar .navbar-brand, .navbar .nav-link, .navbar .nav-link:visited {
  color: #fff !important;
  font-weight: 500;
  letter-spacing: 0.5px;
}
.navbar .nav-link.active, .navbar .nav-link:hover {
  color: #fbbf24 !important;
}
.card, .table {
  border-radius: 1rem;
  box-shadow: 0 4px 24px rgba(0,0,0,0.07);
  background: #fff;
}
.card:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 8px 32px rgba(37,99,235,0.13);
  transition: all 0.2s;
}
.table th {
  background: #2563eb;
  color: #fff;
  border-top-left-radius: .7rem;
  border-top-right-radius: .7rem;
}
.table-striped > tbody > tr:nth-of-type(odd) {
  background-color: #f0fdfa;
}
.btn-primary, .btn-success, .btn-outline-primary {
  border-radius: 2rem;
  font-weight: 500;
  letter-spacing: 0.5px;
  transition: background 0.2s, color 0.2s, box-shadow 0.2s;
}
.btn-primary:hover, .btn-success:hover, .btn-outline-primary:hover {
  box-shadow: 0 2px 12px #2563eb44;
  background: #2563eb;
  color: #fff;
}
.container {
  animation: fadeIn 0.7s cubic-bezier(.4,0,.2,1);
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(24px);}
  to   { opacity: 1; transform: none;}
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg mb-3">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <i class="bi bi-journal-bookmark-fill me-2" style="font-size:1.5rem;color:#fbbf24;"></i>
      Smart Library
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto">
    <?php if(is_logged_in()):
        $u = user();
        if($u['user_type'] === 'librarian'): ?>
            <!-- LIBRARIAN MENU -->
           <li class="nav-item"><a class="nav-link" href="books.php">Manage Books</a></li>
    <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
    <li class="nav-item"><a class="nav-link" href="clearance.php">Clearance</a></li>
            
        <?php elseif($u['user_type'] === 'staff'): ?>
    <!-- STAFF MENU -->
    <li class="nav-item"><a class="nav-link" href="borrow.php">Borrow/Return</a></li>
    <li class="nav-item"><a class="nav-link" href="reservations.php">Reservations</a></li>
    <li class="nav-item"><a class="nav-link" href="penalties.php">Penalties</a></li>
    <li class="nav-item"><a class="nav-link" href="clearance.php">Clearance</a></li>
            
        <?php else: ?>
            <!-- STUDENT/TEACHER MENU -->
    <li class="nav-item"><a class="nav-link" href="borrow.php">My Borrowings</a></li>
    <li class="nav-item"><a class="nav-link" href="reservations.php">My Reservations</a></li>
    <li class="nav-item"><a class="nav-link" href="penalties.php">My Penalties</a></li>
<?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout (<?php echo htmlspecialchars($u['first_name'] ?? $u['username'] ?? ''); ?>)</a></li>
    <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
    <?php endif; ?>
</ul>
    </div>
  </div>
</nav>
<div class="container">
