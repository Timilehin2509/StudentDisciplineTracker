<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is admin
requireAdmin();

// Handle search query
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Get staff from database
$staff = [];
$sql = "SELECT * FROM users WHERE role = 'staff'";

// Add search condition if search is provided
if (!empty($search)) {
    $search = '%' . $conn->real_escape_string($search) . '%';
    $sql .= " AND (name LIKE ? OR username LIKE ? OR email LIKE ?)";
    $stmt = $conn->prepare($sql . " ORDER BY name ASC");
    $stmt->bind_param("sss", $search, $search, $search);
} else {
    $stmt = $conn->prepare($sql . " ORDER BY name ASC");
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $staff[] = $row;
}
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="dashboard-title">Manage Staff</h1>
        <p class="text-muted">View, add, update, or delete staff accounts.</p>
    </div>
    <div>
        <a href="staff_edit.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add New Staff
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="staff.php" method="get" class="d-flex">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search staff..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0 pt-2">Total: <span class="fw-bold"><?php echo count($staff); ?></span> staff members</p>
            </div>
        </div>
        
        <div class="staff-table-container table-responsive">
            <table class="table table-hover sortable">
                <thead>
                    <tr>
                        <th class="sortable-header">Username</th>
                        <th class="sortable-header">Name</th>
                        <th class="sortable-header">Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($staff) > 0): ?>
                        <?php foreach ($staff as $member): ?>
                            <tr id="staff-<?php echo $member['id']; ?>" class="searchable-item">
                                <td><?php echo htmlspecialchars($member['username']); ?></td>
                                <td><?php echo htmlspecialchars($member['name']); ?></td>
                                <td><?php echo htmlspecialchars($member['email']); ?></td>
                                <td>
                                    <a href="staff_edit.php?id=<?php echo $member['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-record" data-id="<?php echo $member['id']; ?>" data-type="staff">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No staff members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
