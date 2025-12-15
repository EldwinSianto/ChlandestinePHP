<?php
// dashboard/users.php
include __DIR__ . '/../config/db.php';

// Ambil Data User + Role + Saldo
$users = $conn->query("
    SELECT u.*, r.role_name, w.balance 
    FROM users u 
    JOIN roles r ON u.role_id = r.role_id 
    LEFT JOIN wallets w ON u.user_id = w.user_id
    ORDER BY u.user_id ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chlandestine | Users</title>
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
    <ul class="navbar-nav ml-auto">
        <li class="nav-item"><a href="../logout.php" class="nav-link text-danger" onclick="return confirm('Logout?')"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../index.php" class="brand-link"><span class="brand-text font-weight-light px-3">🕵️‍♂️ <b>CHLANDESTINE</b></span></a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex"><div class="info"><a href="#" class="d-block">Super Admin</a></div></div>
      
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
          <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt nav-icon"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="users.php" class="nav-link active"><i class="fas fa-users nav-icon"></i><p>Users</p></a></li>
          <li class="nav-item"><a href="invoices.php" class="nav-link"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>Invoices</p></a></li>
          <li class="nav-item"><a href="plans.php" class="nav-link"><i class="fas fa-tags nav-icon"></i><p>Plans</p></a></li>
          <li class="nav-item"><a href="auth_logs.php" class="nav-link"><i class="fas fa-shield-alt nav-icon"></i><p>Security Logs</p></a></li>
          <li class="nav-item"><a href="errors.php" class="nav-link"><i class="fas fa-bug nav-icon"></i><p>System Errors</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><h1>Data Pengguna</h1></div></div>
    <div class="content">
      <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-body p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px">ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>IP Address</th> 
                            <th>Role</th>
                            <th>Sisa Token</th>
                            <th>Tgl Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><b><?php echo htmlspecialchars($row['username']); ?></b></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            
                            <td class="text-muted">
                                <?php echo !empty($row['ip_address']) ? htmlspecialchars($row['ip_address']) : '-'; ?>
                            </td>

                            <td>
                                <span class="badge bg-<?php echo ($row['role_name'] == 'Admin') ? 'danger' : 'success'; ?>">
                                    <?php echo $row['role_name']; ?>
                                </span>
                            </td>
                            <td>💰 <?php echo number_format($row['balance'] ?? 0); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
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