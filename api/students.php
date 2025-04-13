<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        requireAdmin();
        handlePostRequest();
        break;
    case 'PUT':
        requireAdmin();
        handlePutRequest();
        break;
    case 'DELETE':
        requireAdmin();
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
    
    // Handle search query for staff when reporting incidents
    if (isset($_GET['search']) && isStaff()) {
        $search = '%' . $conn->real_escape_string($_GET['search']) . '%';
        
        $stmt = $conn->prepare("
            SELECT id, student_number, name
            FROM students
            WHERE name LIKE ? OR student_number LIKE ?
            ORDER BY name ASC
            LIMIT 10
        ");
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'students' => $students
        ]);
        return;
    }
    
    // If admin is getting a single student
    if (isset($_GET['id']) && isAdmin()) {
        $id = (int)$_GET['id'];
        
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $student = $result->fetch_assoc();
            // Remove password for security
            unset($student['password']);
            
            echo json_encode([
                'success' => true,
                'student' => $student
            ]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode([
                'success' => false,
                'message' => 'Student not found.'
            ]);
        }
        return;
    }
    
    // Only admin can list all students
    if (!isAdmin()) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'success' => false,
            'message' => 'Access denied.'
        ]);
        return;
    }
    
    // List all students for admin
    $students = [];
    $sql = "SELECT * FROM students ORDER BY name ASC";
    $result = $conn->query($sql);
    
    while ($row = $result->fetch_assoc()) {
        // Remove password for security
        unset($row['password']);
        $students[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'students' => $students
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
    if (!isset($inputData['student_number']) || !isset($inputData['name']) || 
        !isset($inputData['email']) || !isset($inputData['class']) || !isset($inputData['password'])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields.'
        ]);
        return;
    }
    
    $student_number = $conn->real_escape_string($inputData['student_number']);
    $name = $conn->real_escape_string($inputData['name']);
    $email = $conn->real_escape_string($inputData['email']);
    $class = $conn->real_escape_string($inputData['class']);
    $password = $inputData['password'];
    
    // Check if student number already exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE student_number = ?");
    $stmt->bind_param("s", $student_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            'success' => false,
            'message' => 'Student number already exists.'
        ]);
        return;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new student
    $stmt = $conn->prepare("
        INSERT INTO students (student_number, name, email, class, password)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $student_number, $name, $email, $class, $hashed_password);
    
    if ($stmt->execute()) {
        $id = $conn->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Student added successfully.',
            'id' => $id
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Error adding student: ' . $conn->error
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
            'message' => 'Student ID is required.'
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
    if (!isset($inputData['student_number']) || !isset($inputData['name']) || 
        !isset($inputData['email']) || !isset($inputData['class'])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields.'
        ]);
        return;
    }
    
    $student_number = $conn->real_escape_string($inputData['student_number']);
    $name = $conn->real_escape_string($inputData['name']);
    $email = $conn->real_escape_string($inputData['email']);
    $class = $conn->real_escape_string($inputData['class']);
    
    // Check if student exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode([
            'success' => false,
            'message' => 'Student not found.'
        ]);
        return;
    }
    
    // Check if student number already exists for another student
    $stmt = $conn->prepare("SELECT id FROM students WHERE student_number = ? AND id != ?");
    $stmt->bind_param("si", $student_number, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            'success' => false,
            'message' => 'Student number already exists for another student.'
        ]);
        return;
    }
    
    // Update student
    if (isset($inputData['password']) && !empty($inputData['password'])) {
        // Update with new password
        $hashed_password = password_hash($inputData['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            UPDATE students 
            SET student_number = ?, name = ?, email = ?, class = ?, password = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sssssi", $student_number, $name, $email, $class, $hashed_password, $id);
    } else {
        // Update without changing password
        $stmt = $conn->prepare("
            UPDATE students 
            SET student_number = ?, name = ?, email = ?, class = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssssi", $student_number, $name, $email, $class, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Student updated successfully.'
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Error updating student: ' . $conn->error
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
            'message' => 'Student ID is required.'
        ]);
        return;
    }
    
    $id = (int)$_GET['id'];
    
    // Check if student exists
    $stmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode([
            'success' => false,
            'message' => 'Student not found.'
        ]);
        return;
    }
    
    // Check if student is involved in any incidents
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM incident_students WHERE student_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        http_response_code(409); // Conflict
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete student as they are involved in one or more incidents.'
        ]);
        return;
    }
    
    // Delete student
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Student deleted successfully.'
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Error deleting student: ' . $conn->error
        ]);
    }
}
?>
