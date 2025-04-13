<?php
// Database connection details
$servername = "localhost";
$username = "root";  // XAMPP default
$password = "";      // XAMPP default (empty password)
$dbname = "disciplinary_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper encoding
$conn->set_charset("utf8mb4");

// Create the database if it doesn't exist yet
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    error_log("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Verify tables exist
$result = $conn->query("SHOW TABLES");
if ($result->num_rows == 0) {
    error_log("ERROR: Required database tables are missing");
    die("System error: Database tables not found. Please contact administrator.");
}
?>
