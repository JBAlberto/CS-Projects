<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents('php://input'), true);

// Validate input
$errors = [];
if (empty($data['email'])) $errors['email'] = 'Email is required.';
if (empty($data['password'])) $errors['password'] = 'Password is required.';
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// DB connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'testdbproject';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Find user
$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $data['email']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'No account found.']);
    exit;
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($data['password'], $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit;
}

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];

$_SESSION['role'] = $user['role']; // optional, in case you want to use it later

echo json_encode([
  'success' => true,
  'username' => $user['username'],
  'role' => $user['role']
]);

