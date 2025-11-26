<?php
session_start();

// Redirect logged-in users to index.php
if (isset($_SESSION['authUser'])) {
    header('Location: index.php');
    exit;
}

// Standardized Database Credentials for InfinityFree
$host = "sql300.infinityfree.com";
$db   = "if0_40502206_flavorful";
$user = "if0_40502206";
$pass = "noelbest2025";

// PDO Options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        try {
            // Prepared statement to prevent SQL Injection
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Successful login. Sanitize user data for session.
                unset($user['password']); // NEVER store password hash in session
                
                // Ensure initials are set, fallback if not
                if (empty($user['initials'])) {
                    $user['initials'] = strtoupper(substr($user['username'], 0, 2));
                }

                $_SESSION['authUser'] = $user;
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            error_log("Login query failed: " . $e->getMessage());
            $error = 'A system error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* Global Styles */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background-color: #f7f7f7; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
    
    /* Header Styles (Simplified for Auth pages) */
    header { display: flex; justify-content: space-between; align-items: center; padding: 20px 60px; background: #fff; border-bottom: 1px solid #eee; }
    header .logo { font-size: 24px; font-weight: 700; text-decoration: none; color: #111; }

    /* Main Content */
    main {
      flex-grow: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .login-container {
      background: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .login-container h2 {
      font-size: 2em;
      margin-bottom: 25px;
      color: #d97706;
    }

    .error {
      background-color: #fef2f2;
      color: #ef4444;
      border: 1px solid #fca5a5;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 0.9em;
      text-align: left;
    }

    form input[type="email"],
    form input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 6px;
      box-sizing: border-box;
      font-family: inherit;
    }

    form button {
      width: 100%;
      padding: 12px;
      background-color: #f59e0b;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1.1em;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    form button:hover {
      background-color: #d97706;
    }

    .links {
      margin-top: 15px;
      text-align: right;
    }

    .links a {
      color: #f59e0b;
      text-decoration: none;
      font-size: 0.9em;
    }
    .links a:hover {
        text-decoration: underline;
    }

    .sign-up-link {
      margin-top: 25px;
      font-size: 0.9em;
      color: #555;
    }

    .sign-up-link a {
      color: #d97706;
      font-weight: 600;
      text-decoration: none;
    }
    .sign-up-link a:hover {
        text-decoration: underline;
    }

    /* Footer Styles */
    footer {
        text-align: center;
        padding: 20px;
        background: #111;
        color: #eee;
        margin-top: auto;
    }

    /* RESPONSIVENESS */
    @media (max-width: 768px) {
        header { padding: 15px 20px; }
        .login-container { padding: 30px; }
    }
  </style>
</head>
<body>
<header>
    <a href="index.php" class="logo">Flavorful</a>
</header>
<main>
  <div class="login-container">
    <h2>Log In</h2>

    <?php if ($error): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Log in</button>
    </form>

    <div class="links">
      <a href="contact.php">Forgot Password? (Contact Support)</a>
    </div>

    <div class="sign-up-link">
      Don't have an account? <a href="register.php">Sign up now</a>
    </div>
  </div>
</main>

<footer>
  &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>