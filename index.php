<?php
include __DIR__ . '/config/db.php';
$plans = $conn->query("SELECT * FROM subscription_plans ORDER BY price ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chlandestine AI Translator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.png">
    
    <link rel="stylesheet" href="assets/css/style.css">
<style>
        /* 1. Atur Tampilan Dasar Card */
        .card {
            background-color: #151515; /* Warna dasar gelap */
            border: 1px solid #333; /* Garis pinggir tipis abu-abu */
            transition: all 0.3s ease; /* Kunci animasinya ada disini */
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        /* 2. Efek Saat Kursor Diarahkan (HOVER) */
        .card:hover {
            transform: translateY(-10px); /* Kartu naik ke atas sedikit */
            border-color: #00dbde; /* Garis pinggir jadi warna Cyan Neon */
            box-shadow: 0 10px 40px rgba(0, 219, 222, 0.4); /* Efek cahaya (Glow) di belakang */
            z-index: 2;
        }

        /* 3. Perbaikan Tombol (Opsional biar makin keren) */
        .card .btn {
            transition: 0.3s;
        }
        .card:hover .btn {
            box-shadow: 0 0 20px rgba(0, 219, 222, 0.6); /* Tombol ikutan nyala */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
        <img src="assets/img/logo.png" alt="Logo" height="40" class="me-2 rounded-circle"> CHLANDESTINE
    </a>
    <div class="ms-auto">
        <a href="auth.php" class="btn btn-primary fw-bold px-4">Login / Sign Up</a>
    </div>
  </div>
</nav>

<div class="text-center py-5" style="margin-top: 80px;">
    <div class="container">
        <h1 class="display-3 fw-bold mb-3">Translating Your Comic<br><span style="color: #00dbde;">Instantly & Seamlessly</span></h1>
        <p class="lead mb-4 text-white-50">AI OCR & Inpainting Translator. Baca komik bahasa asing tanpa hambatan.</p>
        <a href="#" class="btn btn-lg btn-primary fw-bold px-5 py-3">Download Extension</a>
    </div>
</div>

<div class="container my-5">
    <h2 class="text-center mb-5 fw-bold">Pilih Paket Token</h2>
    <div class="row justify-content-center">
        <?php while($row = $plans->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 text-center p-4">
                <h3 class="fw-bold text-uppercase text-white"><?php echo $row['plan_name']; ?></h3>
                <h1 class="display-5 fw-bold my-3 text-white">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></h1>
                <p class="text-white-50"><?php echo number_format($row['token_amount']); ?> Token AI</p>
                <hr style="border-color: #444;">
                <ul class="list-unstyled text-start mx-auto text-white-50" style="max-width: 200px;">
                    <li>✅ Akses OCR Unlimited</li>
                    <li>✅ Auto Inpainting</li>
                    <li>✅ Masa aktif <?php echo $row['duration_days']; ?> Hari</li>
                </ul>
                <a href="auth.php" class="btn btn-primary w-100 mt-3">Pilih Paket</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<footer class="text-white-50 text-center py-4 mt-5" style="border-top: 1px solid #333;">
    <p>&copy; 2025 Chlandestine Team. Final Project Database.</p>
</footer>

</body>
</html>