<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(["error" => "Not authorized"]);
  exit();
}

// Database connection
$host = 'localhost';
$db   = 'testdbproject';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(["error" => "Database connection failed"]);
  exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, contact, location_2, packtype, packagingType, approved, rejected, delivered FROM packages WHERE sender_id = ?");
$stmt->execute([$user_id]);

$packages = $stmt->fetchAll();
echo json_encode($packages);
