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
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $terms = isset($_POST['terms']) ? true : false;

    if (!$username || !$email || !$country || !$phone || !$password || !$terms) {
        $error = 'All fields are required and you must agree to terms.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        try {
            $checkEmail = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
            $checkEmail->execute([$email]);
            
            if ($checkEmail->fetchColumn() > 0) {
                $error = 'Email already registered.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $initials = strtoupper(substr($username, 0, 2));
                
                $stmt = $pdo->prepare('INSERT INTO users (username, email, country, address, phone, password, initials) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$username, $email, $country, $address, $phone, $hashedPassword, $initials]);
                
                $_SESSION['authUser'] = [
                    'id' => $pdo->lastInsertId(),
                    'username' => $username,
                    'email' => $email,
                    'initials' => $initials
                ];
                
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Sign Up</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
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
    
    .main-content { flex-grow: 1; display: flex; justify-content: center; align-items: center; padding: 50px 20px; background-color: #fafafa; }
    .signup-container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); max-width: 500px; width: 100%; }
    .signup-container h2 { text-align: center; margin-bottom: 30px; font-size: 28px; color: #111; }
    
    .input-group { margin-bottom: 20px; }
    .input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; }
    .input-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; box-sizing: border-box; outline: none; transition: border-color 0.3s; }
    .input-group input:focus { border-color: #f59e0b; }
    
    .checkbox { display: flex; align-items: center; margin-bottom: 25px; }
    .checkbox input { margin-right: 10px; }
    .checkbox label { font-size: 14px; color: #555; }
    
    .btn { width: 100%; padding: 12px; background-color: #f59e0b; color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 700; cursor: pointer; transition: background-color 0.3s; }
    .btn:hover { background-color: #e6a100; }
    
    .sign { text-align: center; margin-top: 20px; font-size: 14px; color: #777; }
    .sign a { color: #f59e0b; text-decoration: none; font-weight: 600; }
    .sign a:hover { text-decoration: underline; }
    
    .error { color: #dc2626; background: #fee2e2; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
    .success { color: #059669; background: #d1fae5; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
    
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

  <main class="main-content">
    <div class="signup-container">
      <h2>Create Your Account</h2>
      
      <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      
      <form action="register.php" method="POST">
        <div class="input-group">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Enter your username" required>
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="input-group">
            <label for="country">Country</label>
            <input id="country" type="text" name="country" placeholder="Enter your country" required>
        </div>

         <div class="input-group">
            <label for="address">Address</label>
            <input id="address" type="text" name="address" placeholder="Enter your address">
        </div>

        <div class="input-group">
            <label for="phone">Phone</label>
            <input id="phone" type="tel" name="phone" placeholder="Enter your phone number" required>
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter your password" required>
        </div>

        <div class="checkbox">
          <input type="checkbox" id="terms" name="terms" required>
          <label for="terms">I agree to the Terms & Conditions</label>
        </div>

        <button type="submit" class="btn">CREATE ACCOUNT</button>

        <div class="sign">
          Already have an account? <a href="login.php">Login</a>
        </div>
      </form>
    </div>
  </main>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>