<?php
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if user is admin
requireAdmin();

// Initialize variables
$id = '';
$username = '';
$name = '';
$email = '';
$password = '';
$confirm_password = '';
$isEdit = false;
$error = '';
$success = '';

// Check if this is an edit request
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $isEdit = true;
    
    // Get staff data
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'staff'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $staff = $result->fetch_assoc();
        $username = $staff['username'];
        $name = $staff['name'];
        $email = $staff['email'];
    } else {
        header("Location: staff.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($name) || empty($email)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!$isEdit && empty($password)) {
        $error = 'Please enter a password.';
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Username already exists.';
        } else {
            if ($isEdit) {
                // Update existing staff
                if (!empty($password)) {
                    // Update with new password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("
                        UPDATE users 
                        SET username = ?, name = ?, email = ?, password = ?
                        WHERE id = ? AND role = 'staff'
                    ");
                    $stmt->bind_param("ssssi", $username, $name, $email, $hashed_password, $id);
                } else {
                    // Update without changing password
                    $stmt = $conn->prepare("
                        UPDATE users 
                        SET username = ?, name = ?, email = ?
                        WHERE id = ? AND role = 'staff'
                    ");
                    $stmt->bind_param("sssi", $username, $name, $email, $id);
                }
                
                if ($stmt->execute()) {
                    $success = 'Staff member updated successfully.';
                } else {
                    $error = 'Error updating staff member: ' . $conn->error;
                }
            } else {
                // Create new staff
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'staff';
                $stmt = $conn->prepare("
                    INSERT INTO users (username, password, role, name, email)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("sssss", $username, $hashed_password, $role, $name, $email);
                
                if ($stmt->execute()) {
                    header("Location: staff.php?success=record_created");
                    exit;
                } else {
                    $error = 'Error creating staff member: ' . $conn->error;
                }
            }
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="dashboard-header mb-4">
    <h1 class="dashboard-title"><?php echo $isEdit ? 'Edit Staff' : 'Add New Staff'; ?></h1>
    <p class="text-muted"><?php echo $isEdit ? 'Update staff information' : 'Create a new staff account'; ?></p>
</div>

<div class="card">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" class="needs-validation" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    <div class="invalid-feedback">Please enter a username.</div>
                </div>
                
                <div class="col-md-6">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    <div class="invalid-feedback">Please enter the staff member's full name.</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="password" class="form-label"><?php echo $isEdit ? 'Password (leave blank to keep current)' : 'Password *'; ?></label>
                    <input type="password" class="form-control" id="password" name="password" <?php echo $isEdit ? '' : 'required'; ?>>
                    <div class="invalid-feedback">Please enter a password.</div>
                </div>
                
                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    <div class="invalid-feedback">Passwords do not match.</div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <a href="staff.php" class="btn btn-secondary">Cancel</a>
                </div>
                <div class="col-md-6 text-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> <?php echo $isEdit ? 'Update Staff' : 'Add Staff'; ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
