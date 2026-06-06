<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

$user_id = $_SESSION['user_id'];

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'testdbproject';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  echo json_encode(['error' => 'Database connection failed.']);
  exit;
}

$tracking_number = $_GET['tracking_number'] ?? '';
$tracking_number = trim($tracking_number);
if ($tracking_number === '') {
  echo json_encode(['error' => 'Tracking number is required']);
  exit;
}

// Include sender_id in the condition
$stmt = $conn->prepare("
  SELECT tracking_number, name, location_1, location_2, packtype, approved, rejected, delivered 
  FROM packages 
  WHERE tracking_number = ? AND sender_id = ?
");

if (!$stmt) {
  echo json_encode(['error' => 'Failed to prepare statement']);
  exit;
}

$stmt->bind_param("si", $tracking_number, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode([
    'tracking_number' => $row['tracking_number'],
    'name' => $row['name'],
    'location_1' => $row['location_1'],
    'location_2' => $row['location_2'],
    'packtype' => $row['packtype'],
    'approved' => (int)$row['approved'],
    'rejected' => (int)$row['rejected'],
    'delivered' => (int)$row['delivered']
  ]);
} else {
  echo json_encode(['error' => 'Package not found or access denied']);
}

$stmt->close();
$conn->close();
?>
