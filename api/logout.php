<?php
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

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'No active session found.'
    ]);
    exit;
}

// Get user info before logout for confirmation
$userInfo = [
    'id' => $_SESSION['user_id'],
    'role' => $_SESSION['role']
];

// Log the user out
logout();

// Return success response
echo json_encode([
    'success' => true,
    'message' => 'Logout successful.',
    'user' => $userInfo
]);
?>
