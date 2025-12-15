<?php
session_start();
include __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] == 1) {
    header("Location: auth.php"); exit;
}

$user_id = $_SESSION['user_id'];

// LOGIC BELI PAKET
if (isset($_POST['beli_paket'])) {
    $plan_id = $_POST['plan_id'];
    $price = $_POST['price'];
    $pay_method = $_POST['payment_method']; 
    $inv_id = "INV-" . date('Ymd') . "-" . rand(100, 999);

    $stmt = $conn->prepare("INSERT INTO invoices (invoice_id, user_id, plan_id, amount, status, payment_method) VALUES (?, ?, ?, ?, 'UNPAID', ?)");
    $stmt->bind_param("siids", $inv_id, $user_id, $plan_id, $price, $pay_method);
    
    if ($stmt->execute()) {
        header("Location: user_panel.php?status=success&inv=$inv_id&method=$pay_method");
        exit;
    }
}

if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $inv = htmlspecialchars($_GET['inv']);
    $met = htmlspecialchars($_GET['method']);
    $msg = "✅ Tagihan <b>$inv</b> berhasil dibuat! Silakan bayar via <b>$met</b>.";
}

$wallet = $conn->query("SELECT balance FROM wallets WHERE user_id = $user_id")->fetch_assoc();
$saldo = $wallet['balance'] ?? 0;
$plans = $conn->query("SELECT * FROM subscription_plans");
$my_invoices = $conn->query("SELECT i.*, p.plan_name FROM invoices i JOIN subscription_plans p ON i.plan_id = p.plan_id WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Member Area | Chlandestine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        /* Override style khusus halaman ini */
        .payment-option { cursor: pointer; border: 1px solid #444; border-radius: 10px; padding: 15px; transition: 0.2s; background: #252525; }
        .payment-option:hover { border-color: #00dbde; background-color: #333; }
        .payment-option input { display: none; }
        .payment-option.selected { border-color: #00dbde; background-color: #2a2a2a; box-shadow: 0 0 10px rgba(0, 219, 222, 0.2); }

        /* --- TAMBAHAN FIX TABEL --- */
        .table {
            --bs-table-bg: transparent; 
            --bs-table-color: white;
        }
        .table td, .table th {
            background-color: transparent !important; 
            border-bottom: 1px solid #444; 
            color: white !important; 
        }
        .table-hover tbody tr:hover td {
            background-color: #333 !important; 
            color: #00dbde !important; 
        }

        /* --- STYLE LOGO BARU DI NAVBAR --- */
        .brand-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            margin-right: 25px; /* Jarak dengan sapaan user */
            gap: 10px;
        }
        .brand-logo img { height: 35px; width: auto; transition: 0.3s; }
        .brand-logo span {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 20px;
            color: #fff;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .brand-logo:hover img { transform: scale(1.1); }
        .brand-logo:hover span { color: #00dbde; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <div class="d-flex align-items-center">
            <a href="index.php" class="brand-logo">
                <img src="assets/img/logo.png" alt="Logo">
                <span>CHLANDESTINE</span>
            </a>

            <span class="navbar-brand fw-bold fs-6 m-0">
                <i class="fas fa-user-circle me-2"></i> Halo, <?php echo $_SESSION['username'] ?? 'Member'; ?>
            </span>
        </div>

        <div class="d-flex gap-2">
            <a href="user_settings.php" class="btn btn-outline-light btn-sm"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-white-50 mb-1">Sisa Token Anda</h5>
                <h2 class="text-white fw-bold mb-0">💰 <?php echo number_format($saldo); ?> <small class="fs-6 text-white-50">Token</small></h2>
            </div>
            <i class="fas fa-coins fa-3x" style="color: #ffd700; opacity: 0.8;"></i>
        </div>
    </div>

    <?php if(isset($msg)) echo "<div class='alert alert-success alert-dismissible fade show' style='background: #1e1e1e; color: #00dbde; border: 1px solid #00dbde;'>$msg <button type='button' class='btn-close btn-close-white' data-bs-dismiss='alert'></button></div>"; ?>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header py-3"><i class="fas fa-shopping-cart me-2"></i> Beli Token Baru</div>
                <div class="card-body">
                    <form method="POST">
                        <label class="form-label fw-bold text-white-50">1. Pilih Paket</label>
                        <select name="plan_id" class="form-select mb-4" id="planSelect" required>
                            <option value="">-- Silakan Pilih --</option>
                            <?php while($p = $plans->fetch_assoc()): ?>
                                <option value="<?php echo $p['plan_id']; ?>" data-price="<?php echo $p['price']; ?>">📦 <?php echo $p['plan_name']; ?> - Rp <?php echo number_format($p['price']); ?></option>
                            <?php endwhile; ?>
                        </select>
                        <input type="hidden" name="price" id="priceInput">
                        
                        <label class="form-label fw-bold text-white-50">2. Bayar Pakai Apa?</label>
                        <div class="row g-2 mb-4">
                            <div class="col-6"><label class="payment-option d-flex align-items-center gap-2 w-100"><input type="radio" name="payment_method" value="BCA" required><i class="fas fa-university text-primary"></i> BCA</label></div>
                            <div class="col-6"><label class="payment-option d-flex align-items-center gap-2 w-100"><input type="radio" name="payment_method" value="Mandiri"><i class="fas fa-building text-warning"></i> Mandiri</label></div>
                            <div class="col-6"><label class="payment-option d-flex align-items-center gap-2 w-100"><input type="radio" name="payment_method" value="GoPay"><i class="fas fa-wallet text-success"></i> GoPay</label></div>
                            <div class="col-6"><label class="payment-option d-flex align-items-center gap-2 w-100"><input type="radio" name="payment_method" value="OVO"><i class="fas fa-mobile-alt text-info"></i> OVO</label></div>
                        </div>
                        
                        <button type="submit" name="beli_paket" class="btn btn-primary w-100 py-3 fw-bold">Checkout Sekarang</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-history me-2"></i> Riwayat</span>
                    <a href="user_panel.php" class="btn btn-sm btn-outline-light"><i class="fas fa-sync-alt"></i> Refresh</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead><tr><th class="ps-3 text-white-50">Invoice</th><th class="text-white-50">Status</th></tr></thead>
                            <tbody>
                                <?php while($inv = $my_invoices->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-white"><?php echo $inv['invoice_id']; ?></div>
                                        <small class="text-white-50"><?php echo $inv['plan_name']; ?> via <?php echo $inv['payment_method']; ?></small>
                                    </td>
                                    <td>
                                        <?php if($inv['status'] == 'PAID'): ?>
                                            <span class="badge bg-success rounded-pill">Lunas</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark rounded-pill">Proses</span>
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
</div>

<script>
    // JS buat update harga & pilih payment
    document.getElementById('planSelect').addEventListener('change', function() { 
        var selected = this.options[this.selectedIndex];
        if(selected.getAttribute('data-price')) {
            document.getElementById('priceInput').value = selected.getAttribute('data-price'); 
        }
    });
    
    document.querySelectorAll('.payment-option').forEach(option => { 
        option.addEventListener('click', function() { 
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected')); 
            this.classList.add('selected'); 
            this.querySelector('input').checked = true; 
        }); 
    });
</script>
</body>
</html>