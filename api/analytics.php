<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only admin can access analytics
requireAdmin();

// Check which metric is requested
$metric = $_GET['metric'] ?? '';

switch ($metric) {
    case 'total':
        getTotalIncidents();
        break;
    case 'by-type':
        getIncidentsByType();
        break;
    case 'trend':
        getIncidentTrend();
        break;
    default:
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Invalid metric specified.'
        ]);
}

function getTotalIncidents() {
    global $conn;
    
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM incidents");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'total' => $row['total']
    ]);
}

function getIncidentsByType() {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT type, COUNT(*) as count
        FROM incidents
        GROUP BY type
        ORDER BY count DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $labels = [];
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['type'];
        $data[] = (int)$row['count'];
    }
    
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'data' => $data
    ]);
}

function getIncidentTrend() {
    global $conn;
    
    // Get incidents by month for the last 12 months
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(date_reported, '%Y-%m') as month,
            COUNT(*) as count
        FROM incidents
        WHERE date_reported >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(date_reported, '%Y-%m')
        ORDER BY month ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Create an array to hold all months in the last year
    $allMonths = [];
    $monthData = [];
    
    // Generate all months in the past year
    $startDate = new DateTime(date('Y-m-01', strtotime('-11 months')));
    $endDate = new DateTime(date('Y-m-01'));
    
    while ($startDate <= $endDate) {
        $monthKey = $startDate->format('Y-m');
        $allMonths[$monthKey] = 0;
        $startDate->modify('+1 month');
    }
    
    // Fill in actual data
    while ($row = $result->fetch_assoc()) {
        $allMonths[$row['month']] = (int)$row['count'];
    }
    
    // Format the month labels for display
    $labels = [];
    foreach (array_keys($allMonths) as $month) {
        $date = DateTime::createFromFormat('Y-m', $month);
        $labels[] = $date->format('M Y');
    }
    
    echo json_encode([
        'success' => true,
        'labels' => $labels,
        'data' => array_values($allMonths)
    ]);
}
?>
