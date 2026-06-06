<?php
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
  echo json_encode(["success" => false, "message" => "No package ID provided."]);
  exit;
}

$host = 'localhost';
$db   = 'testdbproject';
$user = 'root';         // adjust as needed
$pass = '';             // adjust as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);

  // Update package to mark rejected = 1 and approved = 0 (to ensure no overlap)
  $stmt = $pdo->prepare("UPDATE packages SET approved = 0, delivered = 1 WHERE id = ?");
  $stmt->execute([$data['id']]);

  echo json_encode(["success" => true]);

} catch (PDOException $e) {
  echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
