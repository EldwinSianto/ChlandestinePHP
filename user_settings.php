<?php
session_start();
include __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] == 1) {
    header("Location: auth.php"); exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

if (isset($_POST['simpan_setting'])) {
    $lang = $_POST['target_language'];
    $font = $_POST['font_size'];
    $stmt = $conn->prepare("INSERT INTO user_settings (user_id, target_language, font_size) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE target_language = ?, font_size = ?");
    $stmt->bind_param("isisi", $user_id, $lang, $font, $lang, $font);
    if ($stmt->execute()) { $msg = "<div class='alert alert-success' style='background:#1e1e1e; color:#00dbde; border:1px solid #00dbde;'>✅ Tersimpan!</div>"; } else { $msg = "<div class='alert alert-danger'>❌ Gagal: " . $conn->error . "</div>"; }
}

$query = $conn->query("SELECT * FROM user_settings WHERE user_id = $user_id");
$data = $query->fetch_assoc();
$current_lang = $data['target_language'] ?? 'id';
$current_font = $data['font_size'] ?? 12;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <a href="user_panel.php" class="navbar-brand btn btn-outline-light border-0"><i class="fas fa-arrow-left me-2"></i> Kembali</a>
        <span class="navbar-text text-white fw-bold">⚙️ Pengaturan Akun</span>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5 class="mb-0 fw-bold">Preferensi Translasi</h5></div>
                <div class="card-body">
                    <?php echo $msg; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Target Bahasa</label>
                            <select name="target_language" class="form-select">
                                <option value="id" <?php echo ($current_lang == 'Indonesian') ? 'selected' : ''; ?>>🇮🇩 Bahasa Indonesia</option>
                                <option value="en" <?php echo ($current_lang == 'english') ? 'selected' : ''; ?>>🇬🇧 English</option>
                                <option value="jp" <?php echo ($current_lang == 'japanese') ? 'selected' : ''; ?>>🇯🇵 Japanese</option>
                                <option value="kr" <?php echo ($current_lang == 'korean') ? 'selected' : ''; ?>>🇰🇷 Korean</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white-50">Ukuran Font (px)</label>
                            <input type="number" name="font_size" class="form-control" min="8" max="30" value="<?php echo $current_font; ?>">
                        </div>
                        <hr style="border-color: #444;">
                        <div class="d-flex justify-content-between">
                            <a href="user_panel.php" class="btn btn-outline-light">Batal</a>
                            <button type="submit" name="simpan_setting" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>