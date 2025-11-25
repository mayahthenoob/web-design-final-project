<?php
// PHP file to establish the database connection.
// IMPORTANT: Replace these credentials with your actual database details.

$servername = "localhost";
$db_username = "your_db_username";
$db_password = "your_db_password";
$dbname = "FlavorfulDB";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    // In a real application, you would log this error and show a generic message to the user.
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8mb4");

// Start a session for user authentication
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>