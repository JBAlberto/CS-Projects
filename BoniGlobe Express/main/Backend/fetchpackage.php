<?php

header("Content-Type: application/json");

// DB connection
$host = 'localhost';
$db   = 'testdbproject';
$user = 'root';         // Adjust as needed
$pass = '';             // Adjust as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);

  // Fetch all packages
  $stmt = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC");
  $packages = $stmt->fetchAll();
  // Example: Make sure delivered is included



  echo json_encode($packages);

} catch (PDOException $e) {
  echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
