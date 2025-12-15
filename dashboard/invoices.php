<?php
include __DIR__ . '/../config/db.php';
if (isset($_GET['acc_id'])) {
    $inv_id = $_GET['acc_id'];
    $cek = $conn->query("SELECT * FROM invoices JOIN subscription_plans ON invoices.plan_id = subscription_plans.plan_id WHERE invoice_id = '$inv_id' AND status='UNPAID'");
    if ($row = $cek->fetch_assoc()) {
        $user_id = $row['user_id']; $token_add = $row['token_amount'];
        $conn->query("UPDATE invoices SET status='PAID' WHERE invoice_id = '$inv_id'");
        $conn->query("UPDATE wallets SET balance = balance + $token_add WHERE user_id = $user_id");
        header("Location: invoices.php"); exit;
    }
}
$data = $conn->query("SELECT i.*, u.username, p.plan_name, p.token_amount FROM invoices i JOIN users u ON i.user_id = u.user_id JOIN subscription_plans p ON i.plan_id = p.plan_id ORDER BY i.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chlandestine | Invoices</title>
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
          <li class="nav-item"><a href="invoices.php" class="nav-link active"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>Invoices</p></a></li>
          <li class="nav-item"><a href="plans.php" class="nav-link"><i class="fas fa-tags nav-icon"></i><p>Plans</p></a></li>
          <li class="nav-item"><a href="auth_logs.php" class="nav-link"><i class="fas fa-shield-alt nav-icon"></i><p>Security Logs</p></a></li>
          <li class="nav-item"><a href="errors.php" class="nav-link"><i class="fas fa-bug nav-icon"></i><p>System Errors</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><h1>Kelola Pembayaran</h1></div></div>
    <div class="content">
      <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead><tr><th>Invoice ID</th><th>User</th><th>Paket</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php while($r = $data->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $r['invoice_id']; ?></td>
                            <td><?php echo $r['username']; ?></td>
                            <td><?php echo $r['plan_name']; ?></td>
                            <td>Rp <?php echo number_format($r['amount']); ?></td>
                            <td><span class="badge bg-<?php echo ($r['status']=='PAID')?'success':'danger'; ?>"><?php echo $r['status']; ?></span></td>
                            <td>
                                <?php if($r['status'] == 'UNPAID'): ?>
                                    <a href="invoices.php?acc_id=<?php echo $r['invoice_id']; ?>" class="btn btn-sm btn-primary" onclick="return confirm('Approve?')">Approve</a>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Done</button>
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