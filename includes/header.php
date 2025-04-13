<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Babcock University - Student Disciplinary System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <i class="fas fa-university me-2"></i>
                    Babcock University
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/admin_menu.php'; ?>
                        <?php elseif (isStaff()): ?>
                            <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/staff_menu.php'; ?>
                        <?php elseif (isStudent()): ?>
                            <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/student_menu.php'; ?>
                        <?php endif; ?>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>
                                    <?php echo $_SESSION['name']; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    <?php else: ?>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    <main class="container py-4">
        <?php
        // Display error messages if any
        if (isset($_GET['error'])) {
            $error = '';
            switch($_GET['error']) {
                case 'login_failed':
                    $error = 'Invalid username/password combination.';
                    break;
                case 'not_logged_in':
                    $error = 'You must be logged in to access this page.';
                    break;
                case 'unauthorized':
                    $error = 'You do not have permission to access this page.';
                    break;
                default:
                    $error = 'An error occurred.';
            }
            echo showError($error);
        }
        
        // Display success messages if any
        if (isset($_GET['success'])) {
            $success = '';
            switch($_GET['success']) {
                case 'logout':
                    $success = 'You have been successfully logged out.';
                    break;
                case 'password_changed':
                    $success = 'Your password has been successfully changed.';
                    break;
                case 'profile_updated':
                    $success = 'Your profile has been successfully updated.';
                    break;
                case 'record_created':
                    $success = 'Record has been successfully created.';
                    break;
                case 'record_updated':
                    $success = 'Record has been successfully updated.';
                    break;
                case 'record_deleted':
                    $success = 'Record has been successfully deleted.';
                    break;
                default:
                    $success = 'Operation completed successfully.';
            }
            echo showSuccess($success);
        }
        ?>
