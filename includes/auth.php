<?php
require_once 'db_connect.php';
require_once 'functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Authenticate a user (admin or staff)
 * @param string $username Username
 * @param string $password Password
 * @return bool True if authentication successful, false otherwise
 */
function authenticateUser($username, $password) {
    global $conn;
    
    $username = $conn->real_escape_string($username);
    
    // Debug login attempt
    error_log("Login attempt for user: $username");
    
    $sql = "SELECT id, username, password, role, name FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    error_log("User query result rows: " . $result->num_rows);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        error_log("Found user: " . $user['username'] . ", Role: " . $user['role']);
        
        // Plaintext password comparison
        if ($password === $user['password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            
            error_log("Authentication successful for user: " . $user['username']);
            return true;
        } else {
            error_log("Password verification failed for user: " . $user['username']);
        }
    } else {
        error_log("User not found: $username");
    }
    
    return false;
}

/**
 * Authenticate a student
 * @param string $student_number Student number
 * @param string $password Password
 * @return bool True if authentication successful, false otherwise
 */
function authenticateStudent($student_number, $password) {
    global $conn;
    
    $student_number = $conn->real_escape_string($student_number);
    
    // Debug login attempt
    error_log("Student login attempt for: $student_number");
    
    $sql = "SELECT id, student_number, password, name FROM students WHERE student_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    error_log("Student query result rows: " . $result->num_rows);
    
    if ($result->num_rows == 1) {
        $student = $result->fetch_assoc();
        error_log("Found student: " . $student['student_number'] . ", Name: " . $student['name']);
        
        // Plaintext password comparison
        if ($password === $student['password']) {
            // Set session variables
            $_SESSION['user_id'] = $student['id'];
            $_SESSION['student_number'] = $student['student_number'];
            $_SESSION['role'] = 'student';
            $_SESSION['name'] = $student['name'];
            
            error_log("Authentication successful for student: " . $student['student_number']);
            return true;
        } else {
            error_log("Password verification failed for student: " . $student['student_number']);
        }
    } else {
        error_log("Student not found: $student_number");
    }
    
    return false;
}

/**
 * Logout the current user
 */
function logout() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
}
?>
