<?php
session_start();
include 'config/db.php'; 

// --- FUNGSI GET IP (SUPPORT NGROK) ---
function getUserIP() {
    // Cek apakah ada header dari Ngrok/Proxy
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Ambil IP pertama jika ada banyak (dipisahkan koma)
        $addr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($addr[0]);
    }
    // Kalau tidak ada, pakai cara biasa
    return $_SERVER['REMOTE_ADDR'];
}

// --- 1. LOGIKA DAFTAR (REGISTER) ---
if (isset($_POST['register'])) {

    $nama  = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $raw_password = $_POST['password']; // password asli (belum di-hash)
    $role_id = 2;

    // ===============================
    // VALIDASI PASSWORD
    // ===============================
    if (
        strlen($raw_password) < 8 ||
        !preg_match('/[A-Z]/', $raw_password)
    ) {
        echo "<script>alert('Password minimal 8 karakter dan harus mengandung 1 huruf besar.');</script>";
    } else {

        // Hash password SETELAH lolos validasi
        $password = password_hash($raw_password, PASSWORD_DEFAULT);

        // Ambil IP user
        $ip_user = getUserIP();

        // ===============================
        // LIMIT REGISTER: 5 AKUN / 10 MENIT / IP
        // ===============================
        $cek_limit = mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total
             FROM ip_access_logs
             WHERE ip_address = '$ip_user'
               AND activity_type = 'REGISTER_SUCCESS'
               AND timestamp > NOW() - INTERVAL 10 MINUTE"
        );

        $data_limit = mysqli_fetch_assoc($cek_limit);
        $jumlah_akun = $data_limit['total'];

        if ($jumlah_akun >= 5) {
            echo "<script>alert('Terlalu banyak pendaftaran dari IP ini. Silakan coba lagi setelah 10 menit.');</script>";
        } else {

            // ===============================
            // CEK EMAIL SUDAH TERDAFTAR
            // ===============================
            $cek_email = mysqli_query(
                $conn,
                "SELECT email FROM users WHERE email = '$email'"
            );

            if (mysqli_num_rows($cek_email) > 0) {
                echo "<script>alert('Email sudah terdaftar!');</script>";
            } else {

                // ===============================
                // INSERT USER
                // ===============================
                $query = "
                    INSERT INTO users (username, email, password_hash, role_id, ip_address)
                    VALUES ('$nama', '$email', '$password', $role_id, '$ip_user')
                ";

                if (mysqli_query($conn, $query)) {

                    $uid = mysqli_insert_id($conn);

                    // Buat wallet (saldo awal 0)
                    mysqli_query(
                        $conn,
                        "INSERT INTO wallets (user_id, balance) VALUES ($uid, 0)"
                    );

                    // Log register sukses
                    mysqli_query(
                        $conn,
                        "INSERT INTO ip_access_logs (ip_address, user_id, activity_type)
                         VALUES ('$ip_user', $uid, 'REGISTER_SUCCESS')"
                    );

                    echo "<script>alert('Daftar berhasil! Silakan login.');</script>";

                } else {
                    echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "');</script>";
                }
            }
        }
    }
}


// --- 2. LOGIKA LOGIN ---
if (isset($_POST['login'])) {

    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $ip_address = getUserIP();

    // ===============================
    // CEK USER BERDASARKAN EMAIL
    // ===============================
    $stmt = mysqli_prepare($conn, "
        SELECT user_id, username, password_hash, role_id
        FROM users
        WHERE email = ?
        LIMIT 1
    ");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        $user_id = (int)$row['user_id'];

        // ===============================
        // VERIFIKASI PASSWORD
        // ===============================
        if (password_verify($password, $row['password_hash'])) {

            // ===============================
            // BUAT AUTH SESSION (DB)
            // ===============================
            $session_id = bin2hex(random_bytes(32)); // 64 char
            $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

            $stmt2 = mysqli_prepare($conn, "
                    INSERT INTO auth_sessions
                    (session_id, user_id, user_agent, expires_at)
                    VALUES (?, ?, ?, ?)
                ");

                if (!$stmt2) {
                    logSystemError(
                        $conn,
                        $user_id,
                        "AUTH_SESSION_CREATE_FAILED",
                        mysqli_error($conn),
                        ["stage" => "prepare"]
                    );
                    die("Terjadi kesalahan sistem.");
                }

                mysqli_stmt_bind_param(
                    $stmt2,
                    "siss",
                    $session_id,
                    $user_id,
                    $user_agent,
                    $expires_at
                );

                if (!mysqli_stmt_execute($stmt2)) {
                    logSystemError(
                        $conn,
                        $user_id,
                        "AUTH_SESSION_CREATE_FAILED",
                        mysqli_stmt_error($stmt2),
                        ["stage" => "execute"]
                    );
                    mysqli_stmt_close($stmt2);
                    die("Gagal membuat sesi login.");
                }

                mysqli_stmt_close($stmt2);


            // ===============================
            // PHP SESSION (HYBRID)
            // ===============================
            $_SESSION['login']      = true;
            $_SESSION['user_id']    = $user_id;
            $_SESSION['username']   = $row['username'];
            $_SESSION['role_id']    = $row['role_id'];
            $_SESSION['auth_session_id'] = $session_id;

            // ===============================
            // COOKIE (OPTIONAL, RECOMMENDED)
            // ===============================
            setcookie(
                "auth_session",
                $session_id,
                [
                    'expires'  => time() + 86400,
                    'path'     => '/',
                    'secure'   => false,   // true jika HTTPS
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );

            // ===============================
            // LOG LOGIN SUKSES
            // ===============================
            mysqli_query($conn, "
                INSERT INTO ip_access_logs (ip_address, user_id, activity_type)
                VALUES ('$ip_address', $user_id, 'LOGIN_SUCCESS')
            ");

            // ===============================
            // REDIRECT
            // ===============================
            if ((int)$row['role_id'] === 1) {
                header("Location: dashboard/index.php");
            } else {
                header("Location: user_panel.php");
            }
            exit;

        } else {
            // ===============================
            // PASSWORD SALAH
            // ===============================
            mysqli_query($conn, "
                INSERT INTO ip_access_logs (ip_address, user_id, activity_type)
                VALUES ('$ip_address', $user_id, 'LOGIN_FAILED_PASSWORD')
            ");
            echo "<script>alert('Password salah!');</script>";
        }

    } else {
        // ===============================
        // EMAIL TIDAK DITEMUKAN
        // ===============================
        mysqli_query($conn, "
            INSERT INTO ip_access_logs (ip_address, user_id, activity_type)
            VALUES ('$ip_address', NULL, 'LOGIN_FAILED_UNKNOWN_EMAIL')
        ");
        echo "<script>alert('Email tidak ditemukan!');</script>";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page Final</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Montserrat:400,800');

        * { box-sizing: border-box; }

        body {
            background: #121212; 
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            margin: 0;
            position: relative; 
            overflow: hidden; 
        }

        /* --- LOGO KIRI ATAS --- */
        .logo-back {
            position: absolute;
            top: 30px;
            left: 40px;
            z-index: 999;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: transform 0.3s ease;
        }
        .logo-back img { height: 50px; width: auto; }
        .logo-back:hover { transform: scale(1.1); }

        /* --- BACKGROUND BLOBS --- */
        body::before, body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            filter: blur(120px); 
            z-index: -1; 
            opacity: 0.6; 
        }
        body::before { background: #00dbde; top: -100px; left: -100px; }
        body::after { background: #fc00ff; bottom: -100px; right: -100px; }

        h1 { font-weight: bold; margin: 0px; color: #fff; }
        p { font-size: 14px; font-weight: 100; line-height: 20px; letter-spacing: 0.5px; margin: 20px 50px 30px 30px; color: #ddd; }
        span { font-size: 12px; color: #aaa; margin-bottom: 15px; display: block; }
        a { color: #fff; font-size: 14px; text-decoration: none; margin: 15px 0; }

        button {
            border-radius: 20px;
            border: none;
            background-image: linear-gradient(to right, #00dbde, #fc00ff);
            color: #fff;
            font-size: 12px;
            font-weight: bold;
            padding: 12px 45px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 80ms ease-in, opacity 0.3s ease;
            cursor: pointer;
            margin-top: 10px;
            box-shadow: 0 4px 15px 0 rgba(0, 219, 222, 0.4); 
        }
        button:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px 0 rgba(0, 219, 222, 0.6);
        }
        button:active { transform: scale(0.95); }
        button:focus { outline: none; }
        
        button.ghost {
            background-image: none;
            background-color: transparent;
            border: 2px solid #FFFFFF;
            color: #FFFFFF;
            box-shadow: none;
        }
        button.ghost:hover { background-color: rgba(255,255,255,0.1); }

        form {
            background-color: #1e1e1e;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px; 
            height: 100%;
            text-align: center;
            color: white;
        }

        input {
            background-color: #333;
            border: 1px solid transparent;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            color: white;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
        }
        input:focus { border-color: #00dbde; }

        /* CONTAINER UTAMA */
        .container {
            background-color: #1e1e1e;
            border-radius: 50px; 
            box-shadow: 0 14px 28px rgba(0,0,0,0.5), 0 10px 10px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
            width: 900px; 
            max-width: 100%;
            min-height: 550px; 
            z-index: 1; 
        }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }
        .sign-in-container { left: 0; width: 50%; z-index: 2; }
        .container.right-panel-active .sign-in-container { transform: translateX(110%); opacity: 0; }
        .sign-up-container { left: 0; width: 50%; opacity: 0; z-index: 1; }
        .container.right-panel-active .sign-up-container { transform: translateX(100%); opacity: 1; z-index: 4; animation: show 0.6s; }
        .sign-up-container form { padding-left: 90px; padding-right: 20px; }

        @keyframes show {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100% { opacity: 1; z-index: 5; }
        }

        /* OVERLAY */
        .overlay-container {
            position: absolute; top: 0; left: 40%; width: 60%; height: 100%; overflow: hidden;
            transition: transform 0.6s ease-in-out; z-index: 100;
            transform: skewX(-15deg); transform-origin: bottom left; border-radius: 0 20px 20px 0; 
        }
        .container.right-panel-active .overlay-container { transform: translateX(-100%) skewX(-15deg); border-radius: 20px 0 0 20px; }
        .overlay {
            background: linear-gradient(to right, #00dbde, #fc00ff);
            background-repeat: no-repeat; background-size: cover; background-position: 0 0;
            color: #FFFFFF; position: relative; left: -100%; height: 100%; width: 200%;
            transform: skewX(15deg) translateX(0); transition: transform 0.6s ease-in-out;
        }
        .container.right-panel-active .overlay { transform: skewX(15deg) translateX(50%); }
        .overlay-panel {
            position: absolute; display: flex; align-items: center; justify-content: center;
            flex-direction: column; padding: 0 50px; text-align: center; top: 0; height: 100%; width: 50%;
            transform: translateX(0); transition: transform 0.6s ease-in-out;
        }
        .overlay-left { transform: translateX(-20%); }
        .container.right-panel-active .overlay-left { transform: translateX(0); }
        .overlay-right { right: 0; transform: translateX(0); }
        .container.right-panel-active .overlay-right { transform: translateX(20%); }
        .overlay-panel h1 { font-size: 40px !important; margin: 0 !important; }
        .overlay-panel p { font-size: 16px !important; margin: 20px 0 30px !important; }
        .overlay-panel { padding: 0px 50px 0px 100px !important; }
    </style>
</head>
<body>

<a href="index.php" class="logo-back">
    <img src="assets/img/logo.png" alt="Kembali ke Beranda">
</a>
<div class="container" id="container">
    <div class="form-container sign-up-container">
        <form action="" method="POST">
            <h1>Buat Akun</h1>
            <span>Gunakan email untuk registrasi</span>
            <input type="text" name="nama" placeholder="Name" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" name="register">Daftar</button>
        </form>
    </div>

    <div class="form-container sign-in-container">
        <form action="" method="POST">
            <h1>Login</h1>
            <span>Selamat datang kembali</span>
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <a href="#">Lupa password?</a>
            <button type="submit" name="login">Login</button>
        </form>
    </div>

    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Sudah Punya Akun?</h1>
                <p>Login untuk mengakses dashboard dan token kamu.</p>
                <button class="ghost" id="signIn">Login Disini</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Halo, Teman!</h1>
                <p>Belum punya akun? </p>
                <button class="ghost" id="signUp">Daftar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');
    signUpButton.addEventListener('click', () => { container.classList.add("right-panel-active"); });
    signInButton.addEventListener('click', () => { container.classList.remove("right-panel-active"); });
</script>

</body>
</html>