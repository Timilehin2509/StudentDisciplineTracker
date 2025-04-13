<?php
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

// Set content type to JSON
header('Content-Type: application/json');

// Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Please use POST.'
    ]);
    exit;
}

// Get input data
$inputData = json_decode(file_get_contents('php://input'), true);

if (!$inputData) {
    // If no JSON data, try POST variables
    $inputData = $_POST;
}

// Check required fields
if (!isset($inputData['username']) || !isset($inputData['password']) || !isset($inputData['role'])) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: username, password, role.'
    ]);
    exit;
}

$username = $inputData['username'];
$password = $inputData['password'];
$role = $inputData['role'];

// Authenticate based on role
$loginSuccess = false;
$userData = null;

if ($role === 'admin' || $role === 'staff') {
    // Admin or Staff login
    $loginSuccess = authenticateUser($username, $password);
    
    if ($loginSuccess) {
        $userData = [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'name' => $_SESSION['name'],
            'role' => $_SESSION['role']
        ];
    }
} elseif ($role === 'student') {
    // Student login
    $loginSuccess = authenticateStudent($username, $password);
    
    if ($loginSuccess) {
        $userData = [
            'id' => $_SESSION['user_id'],
            'student_number' => $_SESSION['student_number'],
            'name' => $_SESSION['name'],
            'role' => $_SESSION['role']
        ];
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'Invalid role specified. Must be admin, staff, or student.'
    ]);
    exit;
}

// Return response based on authentication result
if ($loginSuccess) {
    echo json_encode([
        'success' => true,
        'message' => 'Login successful.',
        'user' => $userData
    ]);
} else {
    http_response_code(401); // Unauthorized
    echo json_encode([
        'success' => false,
        'message' => 'Invalid credentials. Please try again.'
    ]);
}
?>
