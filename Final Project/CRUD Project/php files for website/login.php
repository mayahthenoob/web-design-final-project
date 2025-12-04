<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout
renderModal(); // Ensure modal function is available

// Redirect logged-in users to balance.php
if ($authUser) {
    header('Location: balance.php');
    exit;
}

$error = '';
$redirectMessage = $_SESSION['redirect_message'] ?? '';
$intendedUrl = $_SESSION['intended_url'] ?? 'index.php'; // Get intended URL
unset($_SESSION['redirect_message']);
unset($_SESSION['intended_url']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = strtolower(trim($_POST['identifier'] ?? '')); // Can be email or username
    $password = trim($_POST['password'] ?? '');

    if (!$identifier || !$password) {
        $error = 'Username/Email and password are required.';
    } else {
        try {
            // Check if identifier is an email or username
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                // Login via Email
                $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            } else {
                // Login via Username
                $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
            }

            $stmt->execute([$identifier]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Successful login
                $_SESSION['authUser'] = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'balance' => (float)$user['balance'], // Ensure balance is a float
                    'role' => $user['role']
                ];

                // Redirect to the intended page (e.g., balance.php if logged in from buy-now) or index.php
                // Default redirect is balance.php as requested for buy-now, otherwise use intendedUrl
                $finalRedirect = ($intendedUrl === 'order.php' || $intendedUrl === 'delivery.php' || $intendedUrl === 'calculator.php' || $intendedUrl === 'buy-now.php' || $intendedUrl === 'balance.php') 
                                ? 'balance.php' 
                                : $intendedUrl;


                header('Location: ' . $finalRedirect);
                exit;

            } else {
                $error = 'Invalid username/email or password.';
            }

        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = 'An internal error occurred. Please try again later.';
        }
    }
}

renderHead('Login');
renderHeader('login.php', $authUser);
?>

<main class="container" style="padding: 40px 20px; display: flex; justify-content: center;">
    <div class="login-box" style="max-width: 400px; width: 100%; padding: 30px; border: 1px solid #ddd; border-radius: 10px; box-shadow: var(--shadow-md); background: var(--background-light);">
        <h2 style="font-size: 2em; color: var(--secondary-color); text-align: center; margin-bottom: 25px;">Log In to Your Account</h2>

        <?php if ($redirectMessage): ?>
            <div style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 10px; margin-bottom: 15px; border-radius: 5px; font-weight: 600;">
                <?php echo htmlspecialchars($redirectMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="error" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 15px; border-radius: 5px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" style="display: grid; gap: 15px;">
            <!-- Changed input name to 'identifier' to accept email or username -->
            <input type="text" name="identifier" placeholder="Username or Email Address" required style="padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            <input type="password" name="password" placeholder="Password" required style="padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
            <button type="submit" class="btn" style="width: 100%; padding: 12px; font-size: 1.1em; margin-top: 10px;">LOG IN</button>
        </form>

        <div style="text-align:center; margin-top:20px; font-size:0.95em;">
            Don't have an account? <a href="register.php" style="color: var(--primary-color); font-weight: 600;">Register Here</a>
        </div>
    </div>
</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>