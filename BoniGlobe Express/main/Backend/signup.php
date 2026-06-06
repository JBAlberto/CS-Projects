<?php
header('Content-Type: application/json');

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate
$errors = [];
if (empty($data['username'])) $errors['username'] = 'Username is required.';
if (empty($data['email'])) $errors['email'] = 'Email is required.';
if (empty($data['password'])) $errors['password'] = 'Password is required.';
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Database config
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'testdbproject';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Hash password
$passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

// Insert into database
$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $data['username'], $data['email'], $passwordHash);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Signup successful!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error saving user: ' . $stmt->error]);

}

$stmt->close();
$conn->close();
?>
