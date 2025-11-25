<?php
require 'db_connect.php';

// Check if the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $country = $conn->real_escape_string($_POST['country']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = $_POST['password']; // Get plain text password

    // Hash the password for security
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind the SQL statement to insert a new user
    $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash, country, address, phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $password_hash, $country, $address, $phone);

    if ($stmt->execute()) {
        // Registration successful
        $user_id = $stmt->insert_id;
        
        // Automatically log the user in
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        
        // Redirect to the homepage or dashboard
        header("Location: index.html"); 
        exit();
    } else {
        // Handle errors (e.g., duplicate email/username)
        // In a real app, you would redirect back with an error message
        echo "Error: Could not register user. " . $stmt->error;
    }

    $stmt->close();
} else {
    // If not a POST request, redirect to the sign-up page
    header("Location: sign-up.html");
}

$conn->close();
?>