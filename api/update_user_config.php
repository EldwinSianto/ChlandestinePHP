<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header("Content-Type: application/json");

// ===============================
// AUTH
// ===============================
if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode([
    'success' => false,
    'error' => 'NOT_LOGGED_IN'
  ]);
  exit;
}

// ===============================
// PARSE JSON
// ===============================
$payload = json_decode(file_get_contents('php://input'), true);

$language  = $payload['language']  ?? null;
$fontSize  = $payload['fontSize']  ?? null;

if (!$language || !is_numeric($fontSize)) {
  http_response_code(400);
  echo json_encode([
    'success' => false,
    'error' => 'INVALID_PAYLOAD'
  ]);
  exit;
}

$user_id  = (int)$_SESSION['user_id'];
$fontSize = (int)$fontSize;

// ===============================
// UPDATE user_settings (BENAR)
// ===============================
$stmt = mysqli_prepare(
  $conn,
  "UPDATE user_settings
   SET target_language = ?, font_size = ?
   WHERE user_id = ?"
);

if (!$stmt) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'error' => 'PREPARE_FAILED'
  ]);
  exit;
}

mysqli_stmt_bind_param($stmt, "sii", $language, $fontSize, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// ===============================
// RESPONSE
// ===============================
echo json_encode([
  'success'  => true,
  'language' => $language,
  'fontSize' => $fontSize
]);
