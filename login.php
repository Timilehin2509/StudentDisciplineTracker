<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Redirect to appropriate dashboard if already logged in
if (isLoggedIn()) {
    redirectToDashboard();
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($role) || empty($username) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } else {
        $loginSuccess = false;
        
        if ($role === 'admin' || $role === 'staff') {
            // Admin or Staff login
            $loginSuccess = authenticateUser($username, $password);
        } else if ($role === 'student') {
            // Student login
            $loginSuccess = authenticateStudent($username, $password);
        }
        
        if ($loginSuccess) {
            // Redirect to appropriate dashboard
            redirectToDashboard();
        } else {
            $error = 'Invalid credentials. Please try again.';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-container">
                <div class="login-logo text-center mb-4">
                    <i class="fas fa-university mb-3"></i>
                    <h2 class="text-primary">Babcock University</h2>
                    <p class="text-muted">Student Disciplinary System Login</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body p-4">
                        <!-- Login Tabs -->
                        <ul class="nav nav-tabs login-tabs mb-4" id="loginTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab" aria-controls="admin" aria-selected="true">Admin</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab" aria-controls="staff" aria-selected="false">Staff</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab" aria-controls="student" aria-selected="false">Student</button>
                            </li>
                        </ul>
                        
                        <!-- Login Forms -->
                        <div class="tab-content" id="loginTabsContent">
                            <!-- Admin Login Form -->
                            <div class="tab-pane fade show active" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                                <form action="login.php" method="post" class="needs-validation" novalidate>
                                    <input type="hidden" name="role" value="admin">
                                    
                                    <div class="mb-3">
                                        <label for="admin-username" class="form-label">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="admin-username" name="username" required>
                                        </div>
                                        <div class="invalid-feedback">Please enter your username.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="admin-password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="admin-password" name="password" required>
                                        </div>
                                        <div class="invalid-feedback">Please enter your password.</div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">Login as Admin</button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Staff Login Form -->
                            <div class="tab-pane fade" id="staff" role="tabpanel" aria-labelledby="staff-tab">
                                <form action="login.php" method="post" class="needs-validation" novalidate>
                                    <input type="hidden" name="role" value="staff">
                                    
                                    <div class="mb-3">
                                        <label for="staff-username" class="form-label">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control" id="staff-username" name="username" required>
                                        </div>
                                        <div class="invalid-feedback">Please enter your username.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="staff-password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="staff-password" name="password" required>
                                        </div>
                                        <div class="invalid-feedback">Please enter your password.</div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">Login as Staff</button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Student Login Form -->
                            <div class="tab-pane fade" id="student" role="tabpanel" aria-labelledby="student-tab">
                                <form action="login.php" method="post" class="needs-validation" novalidate>
                                    <input type="hidden" name="role" value="student">
                                    
                                    <div class="mb-3">
                                        <label for="student-number" class="form-label">Student Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                            <input type="text" class="form-control" id="student-number" name="username" required>
                                        </div>
                                        <div class="invalid-feedback">Please enter your student number.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="student-password" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" class="form-control" id="student-password" name="password" required>
                                        </div>
                                        <div class="invalid-feedback">Please enter your password.</div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">Login as Student</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
