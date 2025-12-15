<?php
include __DIR__ . '/../config/db.php';
$errors = $conn->query("SELECT e.*, u.username FROM system_errors e LEFT JOIN users u ON e.user_id = u.user_id ORDER BY e.occurred_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chlandestine | Errors</title>
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>
    html, body { height: 100%; }
    .wrapper { min-height: 100%; position: relative; }
    .main-sidebar { min-height: 100vh !important; height: 100%; position: fixed; top: 0; left: 0; bottom: 0; }
    .content-wrapper { margin-left: 250px; min-height: 100vh !important; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li></ul>
    <ul class="navbar-nav ml-auto"><li class="nav-item"><a href="../logout.php" class="nav-link text-danger" onclick="return confirm('Logout?')"><i class="fas fa-sign-out-alt"></i> Logout</a></li></ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../index.php" class="brand-link">
      <img src="../assets/img/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">CHLANDESTINE</span>
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex"><div class="info"><a href="#" class="d-block">Super Admin</a></div></div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
          <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt nav-icon"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="users.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>Users</p></a></li>
          <li class="nav-item"><a href="invoices.php" class="nav-link"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>Invoices</p></a></li>
          <li class="nav-item"><a href="plans.php" class="nav-link"><i class="fas fa-tags nav-icon"></i><p>Plans</p></a></li>
          <li class="nav-item"><a href="auth_logs.php" class="nav-link"><i class="fas fa-shield-alt nav-icon"></i><p>Security Logs</p></a></li>
          <li class="nav-item"><a href="errors.php" class="nav-link active"><i class="fas fa-bug nav-icon"></i><p>System Errors</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><h1>System Errors</h1></div></div>
    <div class="content">
      <div class="container-fluid">
        <div class="card card-dark card-outline">
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>Time</th><th>User</th><th>Code</th><th>Message</th></tr></thead>
                    <tbody>
                        <?php while($row = $errors->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['occurred_at']; ?></td>
                            <td><?php echo $row['username'] ?? "-"; ?></td>
                            <td><span class="badge bg-danger"><?php echo $row['error_code']; ?></span></td>
                            <td class="text-danger"><?php echo $row['error_message']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>
  </div>
  <footer class="main-footer"><strong>Copyright &copy; 2025 Chlandestine.</strong></footer>
</div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>