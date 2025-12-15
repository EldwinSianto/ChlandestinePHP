<?php
include '../config/db.php';
// Ambil Log Login + Join ke Tabel User biar tau siapa namanya
$logs = $conn->query("SELECT l.*, u.username, u.email FROM ip_access_logs l LEFT JOIN users u ON l.user_id = u.user_id ORDER BY l.timestamp DESC LIMIT 50");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chlandestine | Security Logs</title>
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>.main-sidebar { min-height: 100vh !important; }</style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li></ul>
  </nav>
  
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link"><span class="brand-text font-weight-light px-3">🕵️‍♂️ <b>CHLANDESTINE</b></span></a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
          <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt nav-icon"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="users.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>Users</p></a></li>
          <li class="nav-item"><a href="invoices.php" class="nav-link"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>Invoices</p></a></li>
          <li class="nav-item"><a href="plans.php" class="nav-link"><i class="fas fa-tags nav-icon"></i><p>Plans</p></a></li>
          <li class="nav-item"><a href="auth_logs.php" class="nav-link active"><i class="fas fa-shield-alt nav-icon"></i><p>Security Logs</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><h1>Security & Access Logs</h1></div></div>
    <div class="content">
      <div class="container-fluid">
        <div class="card card-danger card-outline">
            <div class="card-header"><h3 class="card-title">50 Login Terakhir</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped">
                    <thead><tr><th>Waktu</th><th>IP Address</th><th>User</th><th>Aktivitas</th></tr></thead>
                    <tbody>
                        <?php while($row = $logs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['timestamp']; ?></td>
                            <td class="font-monospace"><?php echo $row['ip_address']; ?></td>
                            <td><?php echo $row['username'] ? $row['username'] . " <small class='text-muted'>(".$row['email'].")</small>" : "<span class='text-muted'>Guest</span>"; ?></td>
                            <td>
                                <?php if($row['activity_type'] == 'LOGIN_SUCCESS'): ?>
                                    <span class="badge bg-success">Login Sukses</span>
                                <?php elseif($row['activity_type'] == 'LOGIN_FAILED'): ?>
                                    <span class="badge bg-danger">Gagal Login</span>
                                <?php else: ?>
                                    <span class="badge bg-info"><?php echo $row['activity_type']; ?></span>
                                <?php endif; ?>
                            </td>
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