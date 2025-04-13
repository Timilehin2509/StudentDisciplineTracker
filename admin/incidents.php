<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is admin
requireAdmin();

// Handle status filter
$statusFilter = '';
if (isset($_GET['status']) && in_array($_GET['status'], ['Open', 'Investigate', 'Closed'])) {
    $statusFilter = $_GET['status'];
}

// Handle search query
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Get incidents from database
$incidents = [];
$sql = "
    SELECT i.*, u.name as reporter_name, 
    (SELECT COUNT(*) FROM incident_students WHERE incident_id = i.id) as student_count
    FROM incidents i
    JOIN users u ON i.reporter_id = u.id
";

// Add conditions based on filters
$conditions = [];
$params = [];
$types = '';

if (!empty($statusFilter)) {
    $conditions[] = "i.status = ?";
    $params[] = $statusFilter;
    $types .= 's';
}

if (!empty($search)) {
    $searchTerm = '%' . $search . '%';
    $conditions[] = "(i.type LIKE ? OR i.description LIKE ? OR u.name LIKE ?)";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sss';
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY i.date_reported DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $incidents[] = $row;
}
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard-header mb-4">
    <h1 class="dashboard-title">Incident Management</h1>
    <p class="text-muted">View and manage all reported incidents.</p>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <form action="incidents.php" method="get" class="d-flex">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search incidents..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <label for="status-filter" class="me-2">Status:</label>
                    <select id="status-filter" class="form-select" onchange="window.location.href='incidents.php?status=' + this.value">
                        <option value="">All</option>
                        <option value="Open" <?php echo $statusFilter === 'Open' ? 'selected' : ''; ?>>Open</option>
                        <option value="Investigate" <?php echo $statusFilter === 'Investigate' ? 'selected' : ''; ?>>Investigate</option>
                        <option value="Closed" <?php echo $statusFilter === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <p class="mb-0 pt-2">Total: <span class="fw-bold"><?php echo count($incidents); ?></span> incidents</p>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover sortable">
                <thead>
                    <tr>
                        <th class="sortable-header">ID</th>
                        <th class="sortable-header">Type</th>
                        <th class="sortable-header">Reported By</th>
                        <th class="sortable-header">Date Reported</th>
                        <th class="sortable-header">Date of Incident</th>
                        <th class="sortable-header">Students</th>
                        <th class="sortable-header">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($incidents) > 0): ?>
                        <?php foreach ($incidents as $incident): ?>
                            <tr class="incident-row" data-status="<?php echo $incident['status']; ?>">
                                <td><?php echo $incident['id']; ?></td>
                                <td><?php echo htmlspecialchars($incident['type']); ?></td>
                                <td><?php echo htmlspecialchars($incident['reporter_name']); ?></td>
                                <td><?php echo formatDate($incident['date_reported']); ?></td>
                                <td><?php echo formatDate($incident['date_of_incidence']); ?></td>
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
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No incidents found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
