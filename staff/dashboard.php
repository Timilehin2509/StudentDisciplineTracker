<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is staff
requireStaff();

// Get incidents reported by current staff
$incidents = [];
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

while ($row = $result->fetch_assoc()) {
    $incidents[] = $row;
}

// Get counts for dashboard
$totalIncidents = count($incidents);
$openIncidents = 0;
$investigateIncidents = 0;
$closedIncidents = 0;

foreach ($incidents as $incident) {
    switch ($incident['status']) {
        case 'Open':
            $openIncidents++;
            break;
        case 'Investigate':
            $investigateIncidents++;
            break;
        case 'Closed':
            $closedIncidents++;
            break;
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard-header mb-4">
    <h1 class="dashboard-title">Staff Dashboard</h1>
    <p class="text-muted">Welcome, <?php echo $_SESSION['name']; ?>. Track reported incidents and their status.</p>
</div>

<div class="row mb-4">
    <!-- Quick Actions Card -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="report_incident.php" class="btn btn-primary mb-3 w-100">
                    <i class="fas fa-plus-circle me-1"></i> Report New Incident
                </a>
                
                <div class="list-group mt-3">
                    <a href="dashboard.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-list me-2"></i> All Incidents
                    </a>
                    <a href="dashboard.php?status=Open" class="list-group-item list-group-item-action">
                        <i class="fas fa-clock me-2"></i> Open Incidents
                    </a>
                    <a href="dashboard.php?status=Investigate" class="list-group-item list-group-item-action">
                        <i class="fas fa-search me-2"></i> Under Investigation
                    </a>
                    <a href="dashboard.php?status=Closed" class="list-group-item list-group-item-action">
                        <i class="fas fa-check-circle me-2"></i> Closed Incidents
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="col-lg-8 mb-4">
        <div class="row h-100">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100">
                    <div class="card-metrics">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div class="metric-value"><?php echo $totalIncidents; ?></div>
                        <div class="metric-label">Total Incidents</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card h-100">
                    <div class="card-metrics">
                        <i class="fas fa-clock"></i>
                        <div class="metric-value"><?php echo $openIncidents; ?></div>
                        <div class="metric-label">Open Incidents</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-metrics">
                        <i class="fas fa-check-circle"></i>
                        <div class="metric-value"><?php echo $closedIncidents; ?></div>
                        <div class="metric-label">Closed Incidents</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incidents Table -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Reported Incidents</h5>
        <div>
            <select id="status-filter" class="form-select form-select-sm" style="width: auto;">
                <option value="all">All Statuses</option>
                <option value="Open" <?php echo isset($_GET['status']) && $_GET['status'] === 'Open' ? 'selected' : ''; ?>>Open</option>
                <option value="Investigate" <?php echo isset($_GET['status']) && $_GET['status'] === 'Investigate' ? 'selected' : ''; ?>>Investigate</option>
                <option value="Closed" <?php echo isset($_GET['status']) && $_GET['status'] === 'Closed' ? 'selected' : ''; ?>>Closed</option>
            </select>
        </div>
    </div>
    <div class="card-body">
        <?php if (count($incidents) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Date of Incident</th>
                            <th>Date Reported</th>
                            <th>Students</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidents as $incident): ?>
                            <tr class="incident-row" data-status="<?php echo $incident['status']; ?>">
                                <td><?php echo htmlspecialchars($incident['type']); ?></td>
                                <td><?php echo formatDate($incident['date_of_incidence']); ?></td>
                                <td><?php echo formatDate($incident['date_reported']); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $incident['student_count']; ?></span>
                                </td>
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
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x mb-3 text-muted"></i>
                <h5>No incidents reported yet</h5>
                <p class="text-muted">Start by reporting a new incident using the button above.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
