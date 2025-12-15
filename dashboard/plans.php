<?php
session_start();

include __DIR__ . '/../config/db.php';
require __DIR__ . '/../helpers/system_error.php';

/* =========================
 * 🔐 AUTHORIZATION (ADMIN)
 * ========================= */
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] ?? '') !== 1) {
    logSystemError(
        $conn,
        $_SESSION['user_id'] ?? null,
        'AUTHORIZATION_ERROR',
        'Unauthorized access to admin plans page'
    );
    die('Akses ditolak.');
}

/* =========================
 * 🧠 UPDATE PLAN (POST)
 * ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_SESSION['user_id'];

    $id    = (int) ($_POST['plan_id'] ?? 0);
    $price = (int) ($_POST['price'] ?? -1);
    $token = (int) ($_POST['token_amount'] ?? -1);

    /* 1️⃣ BUSINESS LOGIC VALIDATION */
    if ($price <= 0 || $token <= 0) {
        logSystemError(
            $conn,
            $user_id,
            'LOGIC_ERROR',
            'Invalid plan configuration',
            [
                'plan_id' => $id,
                'price' => $price,
                'token' => $token
            ]
        );

        die("<script>alert('Harga dan token HARUS lebih dari 0');history.back();</script>");
    }

    /* 2️⃣ CEK PLAN EXIST */
    $check = $conn->prepare(
        "SELECT plan_id FROM subscription_plans WHERE plan_id = ?"
    );
    $check->bind_param("i", $id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        logSystemError(
            $conn,
            $user_id,
            'LOGIC_ERROR',
            'Admin attempted to update non-existent plan',
            ['plan_id' => $id]
        );

        die("<script>alert('Paket tidak ditemukan');window.location='plans.php';</script>");
    }
    $check->close();

    /* 3️⃣ DATABASE UPDATE (BILLING) */
    $stmt = $conn->prepare(
        "UPDATE subscription_plans 
         SET price = ?, token_amount = ? 
         WHERE plan_id = ?"
    );

    if (!$stmt) {
        logSystemError(
            $conn,
            $user_id,
            'DB_PREPARE_FAILED',
            'Failed to prepare plan update query',
            ['error' => $conn->error]
        );
        die("Terjadi kesalahan sistem.");
    }

    $stmt->bind_param("iii", $price, $token, $id);

    if (!$stmt->execute()) {
        logSystemError(
            $conn,
            $user_id,
            'DB_EXECUTE_FAILED',
            'Failed to execute plan update',
            [
                'plan_id' => $id,
                'price' => $price,
                'token' => $token,
                'error' => $stmt->error
            ]
        );
        die("Gagal menyimpan perubahan paket.");
    }

    $stmt->close();

    echo "<script>alert('Update Berhasil!'); window.location='plans.php';</script>";
}

/* =========================
 * 📦 LOAD PLANS
 * ========================= */
$plans = $conn->query("SELECT * FROM subscription_plans");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chlandestine | Plans</title>
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
          <li class="nav-item"><a href="plans.php" class="nav-link active"><i class="fas fa-tags nav-icon"></i><p>Plans</p></a></li>
          <li class="nav-item"><a href="auth_logs.php" class="nav-link"><i class="fas fa-shield-alt nav-icon"></i><p>Security Logs</p></a></li>
          <li class="nav-item"><a href="errors.php" class="nav-link"><i class="fas fa-bug nav-icon"></i><p>System Errors</p></a></li>
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
                    <div class="form-group mb-3"><label>Harga (Rp)</label><input type="number" name="price" class="form-control" value="<?php echo $p['price']; ?>"></div>
                    <div class="form-group mb-3"><label>Token</label><input type="number" name="token_amount" class="form-control" value="<?php echo $p['token_amount']; ?>"></div>
                  </div>
                  <div class="card-footer"><button type="submit" name="update_plan" class="btn btn-primary">Simpan</button></div>
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