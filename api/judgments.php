<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only admin can update judgments
requireAdmin();

// Handle POST request (update judgment)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get input data
    $inputData = json_decode(file_get_contents('php://input'), true);
    
    if (!$inputData) {
        // If no JSON data, try POST variables
        $inputData = $_POST;
    }
    
    // Check for multiple judgment update
    if (isset($inputData['judgments']) && is_array($inputData['judgments']) && isset($_GET['id'])) {
        $incident_id = (int)$_GET['id'];
        
        // Verify the incident exists
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
            exit;
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            $updateStmt = $conn->prepare("
                UPDATE incident_students 
                SET punishment = ?, details = ?
                WHERE incident_id = ? AND student_id = ?
            ");
            
            foreach ($inputData['judgments'] as $judgment) {
                if (!isset($judgment['student_id']) || !isset($judgment['punishment'])) {
                    continue;
                }
                
                $student_id = (int)$judgment['student_id'];
                $punishment = $conn->real_escape_string($judgment['punishment']);
                $details = $conn->real_escape_string($judgment['details'] ?? '');
                
                $updateStmt->bind_param("ssii", $punishment, $details, $incident_id, $student_id);
                $updateStmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Judgments updated successfully.'
            ]);
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            http_response_code(500); // Internal Server Error
            echo json_encode([
                'success' => false,
                'message' => 'Error updating judgments: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }
    
    // Check for single judgment update
    if (!isset($inputData['student_id']) || !isset($inputData['punishment'])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields: student_id, punishment.'
        ]);
        exit;
    }
    
    $student_id = (int)$inputData['student_id'];
    $incident_id = isset($inputData['incident_id']) ? (int)$inputData['incident_id'] : 0;
    $punishment = $conn->real_escape_string($inputData['punishment']);
    $details = isset($inputData['details']) ? $conn->real_escape_string($inputData['details']) : '';
    
    if (!$incident_id) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Missing incident_id.'
        ]);
        exit;
    }
    
    // Verify the relationship exists
    $stmt = $conn->prepare("
        SELECT id FROM incident_students 
        WHERE incident_id = ? AND student_id = ?
    ");
    $stmt->bind_param("ii", $incident_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode([
            'success' => false,
            'message' => 'Student is not associated with this incident.'
        ]);
        exit;
    }
    
    // Update the judgment
    $stmt = $conn->prepare("
        UPDATE incident_students 
        SET punishment = ?, details = ?
        WHERE incident_id = ? AND student_id = ?
    ");
    $stmt->bind_param("ssii", $punishment, $details, $incident_id, $student_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Judgment updated successfully.'
        ]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'message' => 'Error updating judgment: ' . $conn->error
        ]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Please use POST.'
    ]);
}
?>
