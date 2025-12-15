<?php
// File: api/deduct_token.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

include '../config/db.php';

$input = json_decode(file_get_contents("php://input"), true);
$user_id = $input['user_id'] ?? 0;
$url = $input['url'] ?? '-';
$action = $input['action'] ?? 'TRANSLATE';

$BIAYA = 5; // Harga 1x translate (bisa diubah)

// Cek Saldo
$cek = $conn->query("SELECT balance FROM wallets WHERE user_id = $user_id");
$dompet = $cek->fetch_assoc();

if (!$dompet) {
    echo json_encode(["status" => "error", "message" => "User Valid Tidak Ditemukan"]);
    exit;
}

if ($dompet['balance'] >= $BIAYA) {
    // Kurangi Saldo
    $conn->query("UPDATE wallets SET balance = balance - $BIAYA WHERE user_id = $user_id");

    // Catat Log
    $stmt = $conn->prepare("INSERT INTO usage_logs (user_id, tokens_spent, action_type, webtoon_source_url, execution_time_ms) VALUES (?, ?, ?, ?, 1000)");
    $stmt->bind_param("iiss", $user_id, $BIAYA, $action, $url);
    $stmt->execute();

    echo json_encode(["status" => "success", "sisa_token" => $dompet['balance'] - $BIAYA]);
} else {
    echo json_encode(["status" => "error", "message" => "Saldo Habis!"]);
}
?>