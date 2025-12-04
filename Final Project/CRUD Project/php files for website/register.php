<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout
renderModal(); // Ensure modal function is available

// Redirect logged-in users to balance.php
if ($authUser) {
    header('Location: balance.php');
    exit;
}

// Initialize variables
$error = "";
$username = $email = $country = $address = $phone = "";
$terms = false;
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Sanitize and assign POST values
    $username = trim($_POST['username'] ?? "");
    $email    = strtolower(trim($_POST['email'] ?? ""));
    $country  = trim($_POST['country'] ?? "");
    $address  = trim($_POST['address'] ?? "");
    $phone    = trim($_POST['phone'] ?? "");
    $password = $_POST['password'] ?? "";
    $terms    = isset($_POST['terms']);
    
    // Simple Validation
    if (empty($username) || empty($email) || empty($country) || empty($phone) || empty($password)) {
        $error = 'All required fields must be filled.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif (!$terms) {
        $error = 'You must agree to the Terms & Conditions.';
    } else {
        try {
            // Check if email OR username already exists
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ? OR username = ?');
            $stmt->execute([$email, $username]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $error = 'An account with this email or username already exists.';
            } else {
                // Hash the password securely
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user into database
                $stmt = $pdo->prepare('INSERT INTO users (username, email, country, address, phone, password_hash) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$username, $email, $country, $address, $phone, $passwordHash]);
                
                $success = true;
                
                // Automatically log in the new user and redirect to balance page
                $userId = $pdo->lastInsertId();
                $_SESSION['authUser'] = [
                    'user_id' => $userId,
                    'username' => $username,
                    'email' => $email,
                    'balance' => 0.00,
                    'role' => 'user'
                ];
                
                $_SESSION['redirect_message'] = 'Registration successful! Welcome to Flavorful.';
                header('Location: balance.php');
                exit;
            }
            
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = 'A database error occurred during registration. Please try again.';
        }
    }
}

renderHead('Register');
renderHeader('register.php', $authUser);
?>

<main class="container" style="padding: 40px 20px; display: flex; justify-content: center;">
    <div class="register-box" style="max-width: 600px; width: 100%; padding: 30px; border: 1px solid #ddd; border-radius: 10px; box-shadow: var(--shadow-md); background: var(--background-light);">
        <h2 style="font-size: 2em; color: var(--secondary-color); text-align: center; margin-bottom: 25px;">Create Your Account</h2>

        <?php if ($error): ?>
        <div class="error" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 5px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            
            <div class="input-group">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Username</label>
                <input type="text" name="username" required value="<?= htmlspecialchars($username); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div class="input-group">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Email Address</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($email); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div class="input-group">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Country</label>
                <input type="text" name="country" required value="<?= htmlspecialchars($country); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div class="input-group">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Address</label>
                <input type="text" name="address" required value="<?= htmlspecialchars($address); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>
            
            <div class="input-group">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Phone</label>
                <input type="tel" name="phone" required value="<?= htmlspecialchars($phone); ?>" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div class="input-group">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Password (Min 8 Chars)</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div class="checkbox" style="grid-column: span 2; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="terms" required <?= $terms ? 'checked' : ''; ?> style="width: 20px; height: 20px;">
                <label>I agree to the Terms & Conditions</label>
            </div>
            
            <button type="submit" class="btn" style="grid-column: span 2; padding: 12px; font-size: 1.1em; margin-top: 10px;">CREATE ACCOUNT</button>
            
            <div style="text-align:center; margin-top:15px; font-size:1em; grid-column: span 2;">
                Already have an account? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Login</a>
            </div>
        </form>
    </div>
</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>