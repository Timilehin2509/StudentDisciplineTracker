<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only admin can access this API
requireAdmin();

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        handlePostRequest();
        break;
    case 'PUT':
        handlePutRequest();
        break;
    case 'DELETE':
        handleDeleteRequest();
        break;
    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed.'
        ]);
}

function handleGetRequest() {
    global $conn;
    
    // If getting a single staff member
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'staff'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $staff = $result->fetch_assoc();
            // Remove password for security
            unset($staff['password']);
            
            echo json_encode([
                'success' => true,
                'staff' => $staff
            ]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode([
                'success' => false,
                'message' => 'Staff member not found.'
            ]);
        }
        return;
    }
    
    // List all staff members
    $staff = [];
    $sql = "SELECT * FROM users WHERE role = 'staff' ORDER BY name ASC";
    $result = $conn->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        // Remove password for security
        unset($row['password']);
        $staff[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'staff' => $staff
    ]);
}

function handlePostRequest() {
    global $conn;
    
    // Get input data
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    if (!$inputData) {
        // If no JSON data, try POST variables
        $inputData = $_POST;
    }
    
    // Check required fields
    if (!isset($inputData['username']) || !isset($inputData['name']) || 
        !isset($inputData['email']) || !isset($inputData['password'])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields.'
        ]);
        return;
    }
    
    $username = $conn->real_escape_string($inputData['username']);
    $name = $conn->real_escape_string($inputData['name']);
    $email = $conn->real_escape_string($inputData['email']);
    $password = $inputData['password'];
    $role = 'staff'; // Always staff for this endpoint
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists.'
        ]);
        return;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new staff member
    $stmt = $conn->prepare("
        INSERT INTO users (username, password, role, name, email)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $username, $hashed_password, $role, $name, $email);
    
    if ($stmt->execute()) {
        $id = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Staff member added successfully.',
            'id' => $id
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Error adding staff member: ' . $conn->error
        ]);
    }
}

function handlePutRequest() {
    global $conn;
    
    // Check for ID parameter
    if (!isset($_GET['id'])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Staff ID is required.'
        ]);
        return;
    }
    
    $id = (int)$_GET['id'];
    
    // Get input data
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    if (!$inputData) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Invalid input data.'
        ]);
        return;
    }
    
    // Check required fields
    if (!isset($inputData['username']) || !isset($inputData['name']) || !isset($inputData['email'])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields.'
        ]);
        return;
    }
    
    $username = $conn->real_escape_string($inputData['username']);
    $name = $conn->real_escape_string($inputData['name']);
    $email = $conn->real_escape_string($inputData['email']);
    
    // Check if staff member exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'staff'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode([
            'success' => false,
            'message' => 'Staff member not found.'
        ]);
        return;
    }
    
    // Check if username already exists for another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists for another user.'
        ]);
        return;
    }
    
    // Update staff member
    if (isset($inputData['password']) && !empty($inputData['password'])) {
        // Update with new password
        $hashed_password = password_hash($inputData['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            UPDATE users 
            SET username = ?, name = ?, email = ?, password = ?
            WHERE id = ? AND role = 'staff'
        ");
        $stmt->bind_param("ssssi", $username, $name, $email, $hashed_password, $id);
    } else {
        // Update without changing password
        $stmt = $conn->prepare("
            UPDATE users 
            SET username = ?, name = ?, email = ?
            WHERE id = ? AND role = 'staff'
        ");
        $stmt->bind_param("sssi", $username, $name, $email, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Staff member updated successfully.'
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Error updating staff member: ' . $conn->error
        ]);
    }
}

function handleDeleteRequest() {
    global $conn;
    
    // Check for ID parameter
    if (!isset($_GET['id'])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Staff ID is required.'
        ]);
        return;
    }
    
    $id = (int)$_GET['id'];
    
    // Check if staff member exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'staff'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode([
            'success' => false,
            'message' => 'Staff member not found.'
        ]);
        return;
    }
    
    // Check if staff has reported any incidents
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM incidents WHERE reporter_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete staff member as they have reported one or more incidents.'
        ]);
        return;
    }
    
    // Delete staff member
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'staff'");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Staff member deleted successfully.'
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting staff member: ' . $conn->error
        ]);
    }
}
?>
