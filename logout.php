<?php
require_once 'includes/auth.php';

// Log out the user
logout();

// Redirect to login page
header("Location: login.php?success=logout");
exit;
?>
