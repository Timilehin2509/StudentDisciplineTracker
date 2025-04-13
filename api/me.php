<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401); // Unauthorized
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated. Please log in.'
    ]);
    exit;
}

// Get user details based on role
$userData = [
    'id' => $_SESSION['user_id'],
    'name' => $_SESSION['name'],
    'role' => $_SESSION['role']
];

// Add role-specific data
if (isAdmin() || isStaff()) {
    // Get user details from users table
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $userData['username'] = $row['username'];
        $userData['email'] = $row['email'];
    }
} elseif (isStudent()) {
    // Get student details from students table
    $stmt = $conn->prepare("SELECT student_number, email, class FROM students WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $userData['student_number'] = $row['student_number'];
        $userData['email'] = $row['email'];
        $userData['class'] = $row['class'];
    }
}

// Return user data
echo json_encode([
    'success' => true,
    'user' => $userData
]);
?>
