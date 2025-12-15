<?php
session_start();
header("Content-Type: application/json");

require_once '../config/db.php';

$input = json_decode(file_get_contents("php://input"), true);
$email    = $input['email'] ?? '';
$password = $input['password'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'];

if (!$email || !$password) {
    echo json_encode(["success" => false, "error" => "Email atau password kosong"]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT user_id, username, password_hash, role_id 
     FROM users WHERE email = ? LIMIT 1"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if (!$user = $result->fetch_assoc()) {
    echo json_encode(["success" => false, "error" => "Email tidak ditemukan"]);
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    echo json_encode(["success" => false, "error" => "Password salah"]);
    exit;
}

/* ===============================
   SET SESSION (INI KUNCI UTAMA)
================================ */
$_SESSION['user_id']  = $user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role_id']  = $user['role_id'];

$conn->query(
    "INSERT INTO ip_access_logs (ip_address, user_id, activity_type)
     VALUES ('$ip', {$user['user_id']}, 'LOGIN_SUCCESS')"
);

echo json_encode([
    "success"  => true,
    "user_id"  => $user['user_id'],
    "username" => $user['username']
]);
