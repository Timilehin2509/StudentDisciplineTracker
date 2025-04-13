<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize input data
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Verify if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Verify if user has admin role
 * @return bool True if user is admin, false otherwise
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

/**
 * Verify if user has staff role
 * @return bool True if user is staff, false otherwise
 */
function isStaff() {
    return isLoggedIn() && $_SESSION['role'] === 'staff';
}

/**
 * Verify if user has student role
 * @return bool True if user is student, false otherwise
 */
function isStudent() {
    return isLoggedIn() && $_SESSION['role'] === 'student';
}

/**
 * Redirect to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /StudentDisciplineTracker/login.php");
        exit;
    }
}

/**
 * Redirect to appropriate dashboard based on role
 */
function redirectToDashboard() {
    if (isAdmin()) {
        header("Location: /StudentDisciplineTracker/admin/dashboard.php");
    } elseif (isStaff()) {
        header("Location: /StudentDisciplineTracker/staff/dashboard.php");
    } elseif (isStudent()) {
        header("Location: /StudentDisciplineTracker/student/dashboard.php");
    } else {
        header("Location: /StudentDisciplineTracker/login.php");
    }
    exit;
}

/**
 * Require admin role, redirect otherwise
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: /login.php?error=unauthorized");
        exit;
    }
}

/**
 * Require staff role, redirect otherwise
 */
function requireStaff() {
    requireLogin();
    if (!isStaff()) {
        header("Location: /login.php?error=unauthorized");
        exit;
    }
}

/**
 * Require student role, redirect otherwise
 */
function requireStudent() {
    requireLogin();
    if (!isStudent()) {
        header("Location: /login.php?error=unauthorized");
        exit;
    }
}

/**
 * Get current page name
 * @return string Current page name
 */
function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

/**
 * Format date to readable format
 * @param string $date Date to format
 * @return string Formatted date
 */
function formatDate($date) {
    return date("F j, Y", strtotime($date));
}

/**
 * Get incident status badge class
 * @param string $status Incident status
 * @return string CSS class for the badge
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'Open':
            return 'bg-warning';
        case 'Investigate':
            return 'bg-info';
        case 'Closed':
            return 'bg-success';
        default:
            return 'bg-secondary';
    }
}

/**
 * Get punishment badge class
 * @param string $punishment Punishment type
 * @return string CSS class for the badge
 */
function getPunishmentBadgeClass($punishment) {
    switch ($punishment) {
        case 'No Punishment':
            return 'bg-success';
        case 'Suspension':
            return 'bg-warning';
        case 'Expulsion':
            return 'bg-danger';
        case 'Community Service':
            return 'bg-info';
        default:
            return 'bg-secondary';
    }
}

/**
 * Display error message
 * @param string $message Error message to display
 * @return string HTML for error alert
 */
function showError($message) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

/**
 * Display success message
 * @param string $message Success message to display
 * @return string HTML for success alert
 */
function showSuccess($message) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}
?>
