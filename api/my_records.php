<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only students can access their records
requireStudent();

// Get student's disciplinary records
$records = [];
$stmt = $conn->prepare("
    SELECT i.*, is.punishment, is.details, u.name as reporter_name
    FROM incident_students is
    JOIN incidents i ON is.incident_id = i.id
    JOIN users u ON i.reporter_id = u.id
    WHERE is.student_id = ?
    ORDER BY i.date_reported DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}

// Group records by status
$openRecords = [];
$investigateRecords = [];
$closedRecords = [];

foreach ($records as $record) {
    switch ($record['status']) {
        case 'Open':
            $openRecords[] = $record;
            break;
        case 'Investigate':
            $investigateRecords[] = $record;
            break;
        case 'Closed':
            $closedRecords[] = $record;
            break;
    }
}

// Return formatted response
echo json_encode([
    'success' => true,
    'student' => [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['name'],
        'student_number' => $_SESSION['student_number']
    ],
    'records' => [
        'total' => count($records),
        'open' => count($openRecords),
        'investigate' => count($investigateRecords),
        'closed' => count($closedRecords),
        'all' => $records
    ]
]);
?>
