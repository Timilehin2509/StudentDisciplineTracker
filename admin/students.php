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

// Get students from database
$students = [];
$sql = "SELECT * FROM students";

// Add search condition if search is provided
if (!empty($search)) {
    $search = '%' . $conn->real_escape_string($search) . '%';
    $sql .= " WHERE name LIKE ? OR student_number LIKE ? OR email LIKE ? OR class LIKE ?";
    $stmt = $conn->prepare($sql . " ORDER BY name ASC");
    $stmt->bind_param("ssss", $search, $search, $search, $search);
} else {
    $stmt = $conn->prepare($sql . " ORDER BY name ASC");
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="dashboard-title">Manage Students</h1>
        <p class="text-muted">View, add, update, or delete student records.</p>
    </div>
    <div>
        <a href="student_edit.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add New Student
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="students.php" method="get" class="d-flex">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search students..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0 pt-2">Total: <span class="fw-bold"><?php echo count($students); ?></span> students</p>
            </div>
        </div>
        
        <div class="student-table-container table-responsive">
            <table class="table table-hover sortable">
                <thead>
                    <tr>
                        <th class="sortable-header">Student Number</th>
                        <th class="sortable-header">Name</th>
                        <th class="sortable-header">Class</th>
                        <th class="sortable-header">Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $student): ?>
                            <tr id="student-<?php echo $student['id']; ?>" class="searchable-item">
                                <td><?php echo htmlspecialchars($student['student_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['class']); ?></td>
                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <a href="student_edit.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-record" data-id="<?php echo $student['id']; ?>" data-type="student">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
