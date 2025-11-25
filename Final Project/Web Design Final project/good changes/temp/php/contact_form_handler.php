<?php
session_start();

$host = 'localhost';
$db = 'flavorful';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$fullname || !$email || !$message) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO messages (fullname, email, message) VALUES (?, ?, ?)');
            $stmt->execute([$fullname, $email, $message]);
            
            $success = 'Message sent successfully! We will get back to you soon.';
            
            // Optionally send email notification (requires mail server setup)
            // mail('support@flavorful.com', 'New Contact Form Submission', "From: $fullname ($email)\n\nMessage:\n$message");
            
        } catch (PDOException $e) {
            $error = 'Failed to send message: ' . $e->getMessage();
        }
    }
}

// Redirect back to contact page with message
header('Location: contact.php?status=' . ($success ? 'success' : 'error') . '&message=' . urlencode($success ?: $error));
exit;
?>