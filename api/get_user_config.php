<?php
session_start();
require_once __DIR__ . '/../config/db.php';

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
  echo json_encode([
    'success' => false,
    'error' => 'NOT_LOGGED_IN'
  ]);
  exit;
}

$user_id = (int)$_SESSION['user_id'];

$q = mysqli_query(
  $conn,
  "SELECT target_language, font_size 
   FROM user_settings 
   WHERE user_id = $user_id 
   LIMIT 1"
);

if (!$q) {
  echo json_encode([
    'success' => false,
    'error' => 'DB_ERROR'
  ]);
  exit;
}

$row = mysqli_fetch_assoc($q);

echo json_encode([
  'success' => true,
  'language' => $row['target_language'] ?? 'english',
  'font_size' => (int)($row['font_size'] ?? 25)
]);
