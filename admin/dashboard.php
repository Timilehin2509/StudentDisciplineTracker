<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is admin
requireAdmin();

// Get counts for dashboard
$totalStudents = 0;
$totalStaff = 0;
$totalIncidents = 0;
$openIncidents = 0;

// Get total students count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM students");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $totalStudents = $row['count'];
}

// Get total staff count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'staff'");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $totalStaff = $row['count'];
}

// Get total incidents count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM incidents");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $totalIncidents = $row['count'];
}

// Get open incidents count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM incidents WHERE status = 'Open'");
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $openIncidents = $row['count'];
}

// Get recent incidents
$recentIncidents = [];
$stmt = $conn->prepare("
    SELECT i.id, i.type, i.description, i.date_of_incidence, i.date_reported, i.status, u.name as reporter_name
    FROM incidents i
    JOIN users u ON i.reporter_id = u.id
    ORDER BY i.date_reported DESC
    LIMIT 5
");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recentIncidents[] = $row;
}
?>

<?php include '../includes/header.php'; ?>

<div id="dashboard-summary">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Admin Dashboard</h1>
        <p class="text-muted">Welcome, <?php echo $_SESSION['name']; ?>. Here's an overview of the disciplinary system.</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-metrics">
                    <i class="fas fa-user-graduate"></i>
                    <div class="metric-value"><?php echo $totalStudents; ?></div>
                    <div class="metric-label">Students</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-metrics">
                    <i class="fas fa-user-tie"></i>
                    <div class="metric-value"><?php echo $totalStaff; ?></div>
                    <div class="metric-label">Staff</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-metrics">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="metric-value" id="total-incidents"><?php echo $totalIncidents; ?></div>
                    <div class="metric-label">Total Incidents</div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-metrics">
                    <i class="fas fa-clock"></i>
                    <div class="metric-value"><?php echo $openIncidents; ?></div>
                    <div class="metric-label">Open Incidents</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Incidents -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Incidents</h5>
                    <a href="incidents.php" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (count($recentIncidents) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Reported By</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentIncidents as $incident): ?>
                                        <tr>
                                            <td><?php echo $incident['type']; ?></td>
                                            <td><?php echo $incident['reporter_name']; ?></td>
                                            <td><?php echo formatDate($incident['date_reported']); ?></td>
                                            <td>
                                                <span class="badge <?php echo getStatusBadgeClass($incident['status']); ?>">
                                                    <?php echo $incident['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="incident_view.php?id=<?php echo $incident['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">No incidents reported yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="students.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-graduate me-2"></i> Manage Students
                        </a>
                        <a href="staff.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-tie me-2"></i> Manage Staff
                        </a>
                        <a href="incidents.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-exclamation-triangle me-2"></i> View All Incidents
                        </a>
                        <a href="incidents.php?status=Open" class="list-group-item list-group-item-action">
                            <i class="fas fa-clock me-2"></i> Review Open Incidents
                        </a>
                        <a href="reports.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar me-2"></i> View Reports & Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
