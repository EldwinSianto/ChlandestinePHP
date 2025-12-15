<?php
include '../config/db.php';

// LOGIC UPDATE PAKET
if (isset($_POST['update_plan'])) {
    $id = $_POST['plan_id'];
    $price = $_POST['price'];
    $token = $_POST['token_amount'];
    
    $conn->query("UPDATE subscription_plans SET price=$price, token_amount=$token WHERE plan_id=$id");
    echo "<script>alert('Paket Berhasil Diupdate!'); window.location='plans.php';</script>";
}

$plans = $conn->query("SELECT * FROM subscription_plans");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chlandestine | Manage Plans</title>
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <style>.main-sidebar { min-height: 100vh !important; }</style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link"><span class="brand-text font-weight-light px-3">🕵️‍♂️ <b>CHLANDESTINE</b></span></a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
          <li class="nav-item"><a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt nav-icon"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="users.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>Users</p></a></li>
          <li class="nav-item"><a href="invoices.php" class="nav-link"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>Invoices</p></a></li>
          <li class="nav-item"><a href="plans.php" class="nav-link active"><i class="fas fa-tags nav-icon"></i><p>Plans</p></a></li>
          <li class="nav-item"><a href="auth_logs.php" class="nav-link"><i class="fas fa-shield-alt nav-icon"></i><p>Security Logs</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header"><div class="container-fluid"><h1>Kelola Paket Langganan</h1></div></div>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <?php while($p = $plans->fetch_assoc()): ?>
          <div class="col-md-6">
            <div class="card card-warning card-outline">
              <div class="card-header"><h3 class="card-title fw-bold"><?php echo $p['plan_name']; ?></h3></div>
              <form method="POST">
                  <div class="card-body">
                    <input type="hidden" name="plan_id" value="<?php echo $p['plan_id']; ?>">
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="price" class="form-control" value="<?php echo $p['price']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Dapat Token</label>
                        <input type="number" name="token_amount" class="form-control" value="<?php echo $p['token_amount']; ?>">
                    </div>
                  </div>
                  <div class="card-footer">
                    <button type="submit" name="update_plan" class="btn btn-primary">Simpan Perubahan</button>
                  </div>
              </form>
            </div>
          </div>
          <?php endwhile; ?>
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