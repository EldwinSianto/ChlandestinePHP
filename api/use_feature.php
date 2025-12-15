<?php
/**
 * API: use_feature.php
 * Dipanggil oleh background.js (Chrome Extension)
 * Auth: PHP Session ONLY
 * Billing: berdasarkan bubble_count dari OCR
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);

session_start();
require_once __DIR__ . '/../config/db.php';

/* =========================
 * CORS
 * ========================= */
$origin = $_SERVER['HTTP_ORIGIN'] ?? null;
$allowed_origins = [
    'https://comic.naver.com',
    'https://m.comic.naver.com',
];

if ($origin && in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
} else {
    // Chrome extension biasanya origin null
    header("Access-Control-Allow-Origin: http://localhost");
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'METHOD_NOT_ALLOWED']);
    exit;
}

/* =========================
 * PARSE JSON BODY (robust)
 * ========================= */
$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'INVALID_JSON',
        'raw'     => $raw
    ]);
    exit;
}

$action_type  = $payload['action_type'] ?? null;

// ✅ ambil bubble_count dari payload
$bubble_count_raw = $payload['bubble_count'] ?? null;
$bubble_count = is_numeric($bubble_count_raw) ? (int)$bubble_count_raw : 0;

// ✅ ambil source_url dari payload (bukan HTTP_REFERER)
$source_url = (string)($payload['source_url'] ?? '');
if ($source_url === '') {
    // fallback terakhir kalau payload tidak kirim
    $source_url = (string)($_SERVER['HTTP_REFERER'] ?? '');
}

$allowed_actions = ['OCR', 'TRANSLATE', 'INPAINT'];
if (!$action_type || !in_array($action_type, $allowed_actions, true)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'INVALID_PAYLOAD',
        'debug'   => $payload
    ]);
    exit;
}

// ✅ Validasi bubble_count khusus OCR
if ($action_type === 'OCR' && $bubble_count <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => 'INVALID_BUBBLECOUNT',
        'bubble_count' => $bubble_count,
        'debug'   => $payload
    ]);
    exit;
}

/* =========================
 * AUTH (SESSION)
 * ========================= */
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'NOT_LOGGED_IN']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

/* =========================
 * CEK USER
 * ========================= */
$user_q = mysqli_query($conn, "SELECT role_id FROM users WHERE user_id = $user_id LIMIT 1");
if (!$user_q) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'MYSQL_ERROR']);
    exit;
}

$user = mysqli_fetch_assoc($user_q);
if (!$user || !in_array((int)$user['role_id'], [1, 2], true)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'ACCOUNT_BLOCKED']);
    exit;
}

/* =========================
 * LOCK WALLET
 * ========================= */
mysqli_begin_transaction($conn);

$wallet_q = mysqli_query($conn, "SELECT balance FROM wallets WHERE user_id = $user_id FOR UPDATE");
if (!$wallet_q) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'MYSQL_ERROR']);
    exit;
}

$wallet = mysqli_fetch_assoc($wallet_q);
if (!$wallet || (int)$wallet['balance'] <= 0) {
    mysqli_rollback($conn);
    http_response_code(402);
    echo json_encode(['success' => false, 'error' => 'INSUFFICIENT_BALANCE']);
    exit;
}

/* =========================
 * CALL PYTHON /run
 * ========================= */
$ch = curl_init("http://127.0.0.1:8000/run");

curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'api-key: SUPER_SECRET_KEY'
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'action_type'  => $action_type,
        'source_url'   => $source_url,
        'bubble_count' => $bubble_count
    ])
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlerr  = curl_error($ch);
curl_close($ch);

if ($httpcode !== 200 || !$response) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'PYTHON_ERROR',
        'http'    => $httpcode,
        'curl'    => $curlerr
    ]);
    exit;
}

$result = json_decode($response, true);
if (!is_array($result) || !isset($result['tokens_spent'], $result['execution_time_ms'], $result['result'])) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'INVALID_PYTHON_RESPONSE',
        'raw'     => $response
    ]);
    exit;
}

$tokens_spent = (int)$result['tokens_spent'];
$exec_time    = (int)$result['execution_time_ms'];

if ((int)$wallet['balance'] < $tokens_spent) {
    mysqli_rollback($conn);
    http_response_code(402);
    echo json_encode(['success' => false, 'error' => 'INSUFFICIENT_BALANCE']);
    exit;
}

/* =========================
 * UPDATE WALLET
 * ========================= */
$upd = mysqli_prepare($conn, "UPDATE wallets SET balance = balance - ? WHERE user_id = ?");
mysqli_stmt_bind_param($upd, "ii", $tokens_spent, $user_id);
mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

/* =========================
 * INSERT LOG (prepared)
 * ========================= */
$ins = mysqli_prepare($conn, "
  INSERT INTO usage_logs (user_id, tokens_spent, action_type, webtoon_source_url, execution_time_ms)
  VALUES (?, ?, ?, ?, ?)
");
mysqli_stmt_bind_param($ins, "iissi", $user_id, $tokens_spent, $action_type, $source_url, $exec_time);
mysqli_stmt_execute($ins);
mysqli_stmt_close($ins);

mysqli_commit($conn);

/* =========================
 * RESPONSE
 * ========================= */
$new_balance_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT balance FROM wallets WHERE user_id = $user_id"));
$new_balance = (int)($new_balance_row['balance'] ?? 0);

echo json_encode([
    'success'       => true,
    'action'        => $action_type,
    'bubble_count'  => $bubble_count,
    'source_url'    => $source_url,
    'tokens_spent'  => $tokens_spent,
    'balance_left'  => $new_balance,
    'execution_ms'  => $exec_time,
    'result'        => $result['result']
]);
