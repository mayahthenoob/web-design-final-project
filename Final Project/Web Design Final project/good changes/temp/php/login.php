<?php
session_start();

$dsn = "mysql:host=sql300.infinityfree.com;dbname=dbname"; //might have to change these to what th website gives
$username = "noels";
$password = "noelbest2025"

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Email and password are required.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id, username, email, password, initials FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['authUser'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'initials' => $user['initials']
                ];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error = 'Login failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  
  <style id="FLAVORFUL_GLOBAL_STYLE">
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background-color: #fff; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
    
    header { display: flex; justify-content: space-between; align-items: center; padding: 20px 60px; background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 1000; flex-wrap: wrap; }
    header h1 .logo { font-size: 24px; font-weight: 700; text-decoration: none; color: #111; }
    .main-nav a { margin: 0 10px; text-decoration: none; color: #333; font-weight: 500; padding: 5px; transition: color 0.2s; }
    .main-nav a:hover { color: #f59e0b; }
    
    .account-links { display: flex; align-items: center; }
    .account-links a { text-decoration: none; padding: 8px 15px; border-radius: 4px; font-weight: 700; margin-left: 15px; transition: background-color 0.2s, color 0.2s; }
    
    .signup-link { color: #f59e0b; border: 1px solid #f59e0b; }
    .signup-link:hover { background-color: #f59e0b; color: white; }
    
    .login-link { color: white; background-color: #111; }
    .login-link:hover { background-color: #333; }
    
    .hidden { display: none !important; }
    footer { text-align: center; padding: 20px; background: #fff; border-top: 1px solid #eee; font-size: 14px; color: #777; margin-top: auto; }
    
    main { flex-grow: 1; display: flex; justify-content: center; align-items: center; padding: 50px 20px; background-color: #fafafa; }
    
    .login-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); max-width: 400px; width: 100%; text-align: center; }
    .login-container img { width: 100px; height: 100px; margin-bottom: 30px; border-radius: 50%; }
    
    .login-container input[type="email"],
    .login-container input[type="password"] { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box; outline: none; transition: border-color 0.3s; }
    .login-container input:focus { border-color: #f59e0b; }
    
    .login-container button { width: 100%; padding: 12px; background-color: #f59e0b; color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 700; cursor: pointer; transition: background-color 0.3s; }
    .login-container button:hover { background-color: #e6a100; }
    
    .links { margin-top: 20px; font-size: 14px; }
    .links a { color: #f59e0b; text-decoration: none; margin: 0 10px; font-weight: 600; }
    .links a:hover { text-decoration: underline; }
    
    .sign-up-link { margin-top: 15px; font-size: 14px; color: #777; }
    .sign-up-link a { color: #111; text-decoration: none; font-weight: 600; }
    .sign-up-link a:hover { text-decoration: underline; }
    
    .error { color: #dc2626; background: #fee2e2; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
    
    @media (max-width: 768px) {
      header { flex-direction: column; align-items: flex-start; padding: 15px 20px; }
      header h1 { margin-bottom: 10px; }
      .main-nav { order: 2; display: flex; flex-wrap: wrap; margin-bottom: 10px; }
      .account-links { order: 1; margin-bottom: 10px; align-self: flex-end; }
    }
  </style>
</head>

<body>
  <header>
    <h1><a href="index.php" class="logo">Flavorful</a></h1>
    <nav class="main-nav">
        <a href="index.php">Home</a>
        <a href="workers.php">Workers</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="prices.php">Prices</a>
        <a href="socials.php">Socials</a>
        <a href="buy-now.php">Buy Now</a>
    </nav>
    <div class="account-links">
        <a href="sign-up.php" class="signup-link">Sign Up</a>
        <a href="login.php" class="login-link">Login</a>
    </div>
  </header>

  <main>
    <div class="login-container">
      <img src="https://placehold.co/100x100/111/ffffff?text=FL" alt="Login Logo" onerror="this.onerror=null;this.src='https://placehold.co/100x100/111/ffffff?text=FL';">

      <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Log in</button>
      </form>

      <div class="links">
        <a href="#">Forgot Password?</a>
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