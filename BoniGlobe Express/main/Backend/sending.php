<?php
// sending.php

session_start();
header("Content-Type: application/json");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

// Read the incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!$data) {
    echo json_encode(["message" => "Invalid input."]);
    exit;
}

// Check required fields
$requiredFields = ['name', 'contact', 'number', 'location_1', 'location_2', 'packtype', 'packagingType'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === '') {
        echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required.']);
        exit;
    }
}

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

    $tracking_number = uniqid("PKG-");
    $sender_id = $_SESSION['user_id']; // Only trust session for sender identity

    // Insert into packages table (make sure your DB schema includes 'packagingType')
    $stmt = $pdo->prepare("INSERT INTO packages 
        (name, contact, number, location_1, location_2, packtype, packagingType, tracking_number, sender_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $data['name'],
        $data['contact'],
        $data['number'],
        $data['location_1'],
        $data['location_2'],
        $data['packtype'],
        $data['packagingType'],
        $tracking_number,
        $sender_id
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Package info saved successfully. Tracking Number: $tracking_number"
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
