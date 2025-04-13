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

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest();
        break;
    case 'POST':
        if (isAdmin()) {
            handleUpdateRequest();
        } else if (isStaff()) {
            handleCreateRequest();
        } else {
            http_response_code(403); // Forbidden
            echo json_encode([
                'success' => false,
                'message' => 'Access denied.'
            ]);
        }
        break;
    case 'PUT':
        if (isAdmin()) {
            handleUpdateRequest();
        } else {
            http_response_code(403); // Forbidden
            echo json_encode([
                'success' => false,
                'message' => 'Access denied.'
            ]);
        }
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
    
    // If getting a single incident
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        
        // Prepare base query for incident details
        $sql = "
            SELECT i.*, u.name as reporter_name
            FROM incidents i
            JOIN users u ON i.reporter_id = u.id
            WHERE i.id = ?
        ";
        
        // Add access control based on role
        if (isStaff()) {
            // Staff can only view incidents they reported
            $sql .= " AND i.reporter_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        } elseif (isStudent()) {
            // Students can only view incidents they're involved in
            $sql .= " AND EXISTS (
                SELECT 1 FROM incident_students 
                WHERE incident_id = i.id AND student_id = ?
            )";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        } else {
            // Admin can view all incidents
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $incident = $result->fetch_assoc();
            
            // Get students involved
            $stmt = $conn->prepare("
                SELECT s.id, s.student_number, s.name, is.punishment, is.details
                FROM incident_students is
                JOIN students s ON is.student_id = s.id
                WHERE is.incident_id = ?
            ");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $studentsResult = $stmt->get_result();
            
            $students = [];
            while ($student = $studentsResult->fetch_assoc()) {
                $students[] = $student;
            }
            
            $incident['students'] = $students;
            
            echo json_encode([
                'success' => true,
                'incident' => $incident
            ]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode([
                'success' => false,
                'message' => 'Incident not found or you do not have permission to view it.'
            ]);
        }
        return;
    }
    
    // If getting incidents list for staff (mine parameter)
    if (isset($_GET['mine']) && isStaff()) {
        $stmt = $conn->prepare("
            SELECT i.*, 
            (SELECT COUNT(*) FROM incident_students WHERE incident_id = i.id) as student_count
            FROM incidents i
            WHERE i.reporter_id = ?
            ORDER BY i.date_reported DESC
        ");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $incidents = [];
        while ($row = $result->fetch_assoc()) {
            $incidents[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'incidents' => $incidents
        ]);
        return;
    }
    
    // Admin can list all incidents with optional status filter
    if (isAdmin()) {
        $sql = "
            SELECT i.*, u.name as reporter_name, 
            (SELECT COUNT(*) FROM incident_students WHERE incident_id = i.id) as student_count
            FROM incidents i
            JOIN users u ON i.reporter_id = u.id
        ";
        
        $params = [];
        $types = "";
        
        // Add status filter if provided
        if (isset($_GET['status']) && in_array($_GET['status'], ['Open', 'Investigate', 'Closed'])) {
            $sql .= " WHERE i.status = ?";
            $params[] = $_GET['status'];
            $types .= "s";
        }
        
        $sql .= " ORDER BY i.date_reported DESC";
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $incidents = [];
        while ($row = $result->fetch_assoc()) {
            $incidents[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'incidents' => $incidents
        ]);
        return;
    }
    
    // Default response if no specific parameter is provided
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters.'
    ]);
}

function handleCreateRequest() {
    global $conn;
    
    // Get input data
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    if (!$inputData) {
        // If no JSON data, try POST variables
        $inputData = $_POST;
    }
    
    // Check required fields
    if (!isset($inputData['type']) || !isset($inputData['description']) || 
        !isset($inputData['date_of_incidence']) || !isset($inputData['students'])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields.'
        ]);
        return;
    }
    
    $type = $conn->real_escape_string($inputData['type']);
    $description = $conn->real_escape_string($inputData['description']);
    $date_of_incidence = $conn->real_escape_string($inputData['date_of_incidence']);
    $students = is_array($inputData['students']) ? $inputData['students'] : [];
    $supporting_documents = '';
    
    // Handle file uploads
    if (isset($_FILES['supporting_documents'])) {
        $uploadedFiles = [];
        
        if (!empty($_FILES['supporting_documents']['name'][0])) {
            $uploadDir = '../uploads/';
            
            // Create uploads directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Process each uploaded file
            foreach ($_FILES['supporting_documents']['name'] as $key => $name) {
                if ($_FILES['supporting_documents']['error'][$key] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['supporting_documents']['tmp_name'][$key];
                    $fileName = basename($name);
                    $newFileName = time() . '_' . $fileName;
                    $targetFilePath = $uploadDir . $newFileName;
                    
                    // Move the uploaded file
                    if (move_uploaded_file($tmpName, $targetFilePath)) {
                        $uploadedFiles[] = $targetFilePath;
                    }
                }
            }
        }
        
        // Convert uploaded files to comma-separated string
        if (!empty($uploadedFiles)) {
            $supporting_documents = implode(',', $uploadedFiles);
        }
    }
    
    // Validate that at least one student is selected
    if (empty($students)) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'At least one student must be involved in the incident.'
        ]);
        return;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert incident
        $stmt = $conn->prepare("
            INSERT INTO incidents 
            (type, description, date_of_incidence, date_reported, status, supporting_documents, reporter_id)
            VALUES (?, ?, ?, CURDATE(), 'Open', ?, ?)
        ");
        $stmt->bind_param("ssssi", $type, $description, $date_of_incidence, $supporting_documents, $_SESSION['user_id']);
        $stmt->execute();
        
        $incident_id = $conn->insert_id;
        
        // Insert student-incident relationships
        $stmt = $conn->prepare("
            INSERT INTO incident_students (incident_id, student_id)
            VALUES (?, ?)
        ");
        
        foreach ($students as $student_id) {
            $stmt->bind_param("ii", $incident_id, $student_id);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Incident reported successfully.',
            'id' => $incident_id
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Error reporting incident: ' . $e->getMessage()
        ]);
    }
}

function handleUpdateRequest() {
    global $conn;
    
    // Check for incident ID
    if (isset($_POST['incident_id'])) {
        $incident_id = (int)$_POST['incident_id'];
    } elseif (isset($_GET['id'])) {
        $incident_id = (int)$_GET['id'];
    } else {
        // Get input data for PUT requests
        $inputData = json_decode(file_get_contents('php://input'), true);
        $incident_id = isset($inputData['id']) ? (int)$inputData['id'] : 0;
    }
    
    if (!$incident_id) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Incident ID is required.'
        ]);
        return;
    }
    
    // Check if incident exists
    $stmt = $conn->prepare("SELECT id FROM incidents WHERE id = ?");
    $stmt->bind_param("i", $incident_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode([
            'success' => false,
            'message' => 'Incident not found.'
        ]);
        return;
    }
    
    // Get input data
    $inputData = null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $inputData = json_decode(file_get_contents('php://input'), true);
    } else {
        // For POST requests
        $inputData = $_POST;
    }
    
    // Update incident status if provided
    if (isset($inputData['status']) && in_array($inputData['status'], ['Open', 'Investigate', 'Closed'])) {
        $status = $inputData['status'];
        
        $stmt = $conn->prepare("
            UPDATE incidents 
            SET status = ?, updated_by = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sii", $status, $_SESSION['user_id'], $incident_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Incident status updated successfully.'
            ]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode([
                'success' => false,
                'message' => 'Error updating incident status: ' . $conn->error
            ]);
        }
        return;
    }
    
    // If no specific update action was performed
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'No valid update action specified.'
    ]);
}
?>
