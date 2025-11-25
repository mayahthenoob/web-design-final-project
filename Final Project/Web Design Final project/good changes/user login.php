<?php
require 'db_connect.php';

// Check if the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize form data
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // Plain text password

    // Prepare and execute the query to fetch user by email
    $stmt = $conn->prepare("SELECT user_id, username, email, password_hash FROM Users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify the password against the stored hash
        if (password_verify($password, $user['password_hash'])) {
            // Login successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect to the homepage or dashboard
            header("Location: index.html"); 
            exit();
        } else {
            // Invalid password
            $error_message = "Invalid email or password.";
        }
    } else {
        // User not found
        $error_message = "Invalid email or password.";
    }

    $stmt->close();
} else {
    // If not a POST request, redirect to the login page
    header("Location: login.html");
}

$conn->close();

// If there was an error, you would typically redirect back to login.html with the message
if (isset($error_message)) {
    // For this demonstration, we'll just echo the error.
    // In a real app: header("Location: login.html?error=" . urlencode($error_message));
    echo "Login Error: " . $error_message;
}
?>