<?php
session_start();

$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: buy-now.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Buy Now</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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

    .profile-icon { display: flex; align-items: center; margin-left: 15px; }
    .profile-btn { display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; border-radius: 50%; background-color: #f59e0b; color: white; font-weight: 700; text-decoration: none; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); }
    .profile-btn span { font-size: 16px; }
    
    .hidden { display: none !important; }
    footer { text-align: center; padding: 20px; background: #fff; border-top: 1px solid #eee; font-size: 14px; color: #777; margin-top: auto; }

    @media (max-width: 1024px) {
      header { padding: 15px 30px; }
      .main-nav a { margin: 0 5px; font-size: 14px; }
    }

    @media (max-width: 768px) {
      header { flex-direction: column; align-items: flex-start; padding: 15px 20px; }
      header h1 { margin-bottom: 10px; }
      .main-nav { order: 2; display: flex; flex-wrap: wrap; margin-bottom: 10px; }
      .account-links { order: 1; margin-bottom: 10px; align-self: flex-end; }
    }

    .hero { display: flex; align-items: center; justify-content: space-between; padding: 60px; background: #fafafa; margin-bottom: 40px; }
    .hero-text { max-width: 45%; }
    .hero-text h2 { font-size: 62px; font-weight: 800; margin-bottom: 15px; }
    .hero-text p { font-size: 18px; color: #555; margin-bottom: 30px; }
    .hero-text .btn { display: inline-block; text-decoration: none; color: #fff; background: #f59e0b; padding: 10px 25px; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .hero-text .btn:hover { background: #e6a100; }
    
    .main-content { padding: 2rem; }
    .grid { display: grid; gap: 1rem; }
    
    .card { display: flex; flex-direction: column; align-items: center; justify-content: center; background: #222; border-radius: 10px; padding: 2rem; font-size: 1.2rem; text-transform: uppercase; color: #fff; transition: background 0.3s ease, transform 0.3s ease; cursor: pointer; text-decoration: none; }
    .card:hover { background: #f59e0b; transform: translateY(-5px); color: #fff; }
    .card i { font-size: 3rem; margin-top: 1rem; color: #f59e0b; transition: transform 0.3s ease, color 0.3s ease; }
    .card:hover i { color: #fff; transform: scale(1.1); }

    @media (min-width: 768px) {
      .grid { grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    }

    @media (min-width: 1024px) {
      .grid { grid-template-columns: repeat(3, 1fr); }
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
        <a href="register.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
        <a href="login.php" class="login-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Login</a>
        
        <div class="profile-icon" <?php echo !$authUser ? 'style="display:none;"' : ''; ?>>
            <a href="balance.php" class="profile-btn" id="profile-btn">
                <span id="profile-initials"><?php echo htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span>
            </a>
            <form method="POST" style="display:inline;">
                <button type="submit" name="logout" style="margin-left: 10px; padding: 5px 10px; background-color: #f59e0b; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Logout</button>
            </form>
        </div>
    </div>
  </header>

  <section class="hero">
    <div class="hero-text">
      <h2>Place Your Order</h2>
      <p>Select an option below to proceed with ordering, use our calculator, or manage your account.</p> 
      <a href="order.php" class="btn">Start Ordering</a>
    </div>
  </section>

  <main class="main-content">
    <div class="grid">
      <a href="order.php" class="card">
        <span>Order Now</span>
        <i class='bx bx-cart'></i>
      </a>
      <a href="calculator.php" class="card">
        <span>Calculator</span>
        <i class='bx bx-calculator'></i>
      </a>
      <a href="delivery.php" class="card">
        <span>Delivery Info</span>
        <i class='bx bx-car'></i>
      </a>
      <?php if ($authUser): ?>
        <a href="balance.php" class="card">
          <span>My Account</span>
          <i class='bx bx-user-circle'></i>
        </a>
      <?php else: ?>
        <a href="login.php" class="card">
          <span>Login</span>
          <i class='bx bx-log-in'></i>
        </a>
      <?php endif; ?>
    </div>
  </main>
  
  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>