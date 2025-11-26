<?php
session_start();

// Database credentials
$host = "sql300.infinityfree.com";
$dbName = "if0_40502206_flavorful";
$dbUsername = "if0_40502206";
$dbPassword = "noelbest2025";

// PDO options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

// Connect to the database
try {
    $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUsername, $dbPassword, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// Initialize variables
$error = "";
$username = $email = $country = $address = $phone = "";
$terms = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize and assign POST values
    $username = trim($_POST['username'] ?? "");
    $email    = trim($_POST['email'] ?? "");
    $country  = trim($_POST['country'] ?? "");
    $address  = trim($_POST['address'] ?? "");
    $phone    = trim($_POST['phone'] ?? "");
    $password = $_POST['password'] ?? "";
    $terms    = isset($_POST['terms']);

    // Validation
    if (!$username || !$email || !$country || !$phone || !$password || !$terms) {
        $error = "All fields are required, and you must agree to the Terms.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } else {
        // Check if email or username already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Username or Email already taken.";
        } else {
            // Generate password hash
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Generate initials (first two letters of username)
            $initials = strtoupper(substr($username, 0, 2));

            // Insert user into database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, country, address, phone, password, initials) VALUES (?, ?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$username, $email, $country, $address, $phone, $passwordHash, $initials]);
                // Redirect to login page after successful registration
                header('Location: login.php?registered=true');
                exit;
            } catch (PDOException $e) {
                error_log("Registration failed: " . $e->getMessage());
                $error = "An unexpected error occurred during registration. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flavorful - Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #f7f7f7; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }

        /* Header */
        header { display: flex; justify-content: space-between; align-items: center; padding: 20px 60px; background: #fff; border-bottom: 1px solid #eee; }
        header .logo { font-size: 24px; font-weight: 700; text-decoration: none; color: #111; }

        /* Main */
        main { flex-grow: 1; display: flex; justify-content: center; align-items: center; padding: 40px 20px; }
        .register-container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; text-align: center; }
        .register-container h2 { font-size: 2em; margin-bottom: 25px; color: #d97706; }
        .error { background-color: #fef2f2; color: #ef4444; border: 1px solid #fca5a5; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9em; text-align: left; }

        form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-group { text-align: left; }
        .input-group:nth-child(6), /* Phone */ 
        .input-group:nth-child(7), /* Password */ 
        .checkbox, 
        .btn { grid-column: span 2; }
        .input-group label { display: block; font-size: 0.9em; font-weight: 600; margin-bottom: 5px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: inherit; }

        .checkbox { display: flex; align-items: center; gap: 10px; text-align: left; margin-top: 5px; }
        .checkbox input[type="checkbox"] { width: auto; margin: 0; }
        .checkbox label { font-weight: 400; font-size: 0.9em; }

        .btn { width: 100%; padding: 12px; background-color: #f59e0b; color: white; border: none; border-radius: 6px; font-size: 1.1em; font-weight: 600; cursor: pointer; transition: background-color 0.3s; margin-top: 10px; }
        .btn:hover { background-color: #d97706; }

        footer { text-align: center; padding: 20px; background: #111; color: #eee; margin-top: auto; }

        @media (max-width: 768px) {
            header { padding: 15px 20px; }
            .register-container { padding: 30px; }
            form { grid-template-columns: 1fr; }
            .input-group, .input-group:nth-child(6), .input-group:nth-child(7), .checkbox, .btn { grid-column: span 1; }
        }
    </style>
</head>
<body>
<header>
    <a href="index.php" class="logo">Flavorful</a>
</header>
<main>
    <div class="register-container">
        <h2>Create Account</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required value="<?= htmlspecialchars($username); ?>">
            </div>
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($email); ?>">
            </div>
            <div class="input-group">
                <label>Country</label>
                <input type="text" name="country" required value="<?= htmlspecialchars($country); ?>">
            </div>
            <div class="input-group">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($address); ?>">
            </div>
            <div class="input-group">
                <label>Phone</label>
                <input type="tel" name="phone" required value="<?= htmlspecialchars($phone); ?>">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="checkbox">
                <input type="checkbox" name="terms" required <?= $terms ? 'checked' : ''; ?>>
                <label>I agree to the Terms & Conditions</label>
            </div>
            <button type="submit" class="btn">CREATE ACCOUNT</button>
            <div style="text-align:center; margin-top:15px; font-size:14px; grid-column: span 2;">
                Already have an account? <a href="login.php" style="color:#f59e0b; font-weight:600;">Login</a>
            </div>
        </form>
    </div>
</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>
