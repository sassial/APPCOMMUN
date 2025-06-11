<?php
// Database credentials
$host = 'localhost';      // Host name
$user = 'root';           // Database username
$password = '';           // Database password
$database = 'APPFINALE';  // Database name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set character set to utf8mb4
$conn->set_charset("utf8mb4");
?>
