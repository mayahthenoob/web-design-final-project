<?php
session_start();

// Standardized Database Credentials for InfinityFree
$host = "sql300.infinityfree.com";
$db   = "if0_40502206_flavorful"; // Assuming this is the full DB name
$user = "if0_40502206"; // Standard InfinityFree user format
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
    /* Global Styles */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background-color: #fff; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
    
    /* Header Styles (Consistent) */
    header { display: flex; justify-content: space-between; align-items: center; padding: 20px 60px; background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 1000; flex-wrap: wrap; }
    header .logo { font-size: 24px; font-weight: 700; text-decoration: none; color: #111; flex-shrink: 0; }
    nav { display: flex; gap: 20px; align-items: center; }
    nav a { text-decoration: none; color: #111; font-weight: 600; padding: 5px 10px; border-radius: 4px; transition: background-color 0.3s; }
    nav a:hover { background-color: #f0f0f0; }

    .profile-icon { display: flex; align-items: center; }
    .profile-btn {
        background-color: #f59e0b;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
    }
    .profile-icon button {
        margin-left: 10px; 
        padding: 5px 10px; 
        background-color: #e53e3e; 
        color: white; 
        border: none; 
        border-radius: 4px; 
        cursor: pointer; 
        font-size: 14px;
        transition: background-color 0.3s;
    }
    .profile-icon button:hover { background-color: #c53030; }

    /* Hero Section */
    .hero {
        padding: 60px;
        text-align: center;
        background-color: #fef3c7;
        margin-bottom: 40px;
    }
    .hero-text h2 {
        font-size: 2.5em;
        margin-bottom: 10px;
        color: #d97706;
    }
    .hero-text p {
        font-size: 1.1em;
        margin-bottom: 30px;
    }
    .btn {
        display: inline-block;
        background-color: #f59e0b;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        transition: background-color 0.3s, transform 0.2s;
    }
    .btn:hover {
        background-color: #d97706;
        transform: translateY(-2px);
    }

    /* Main Content Grid */
    .main-content {
      padding: 0 60px 60px;
      margin: 0 auto;
      max-width: 1200px;
      width: 100%;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 30px;
    }
    .card {
      background: #f9f9f9;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      text-align: center;
      transition: transform 0.3s, box-shadow 0.3s;
      text-decoration: none;
      color: #111;
      border: 1px solid #eee;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .card span {
      display: block;
      font-size: 1.2em;
      font-weight: 600;
      margin-bottom: 15px;
    }
    .card i {
      font-size: 3em;
      color: #f59e0b;
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
        header {
            padding: 15px 20px;
            flex-direction: column;
            align-items: flex-start;
        }
        header .logo { margin-bottom: 10px; }
        nav { width: 100%; justify-content: space-between; margin-top: 10px; }
        .hero { padding: 40px 20px; }
        .hero-text h2 { font-size: 2.2em; }
        .main-content { padding: 0 20px 40px; }
        .grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
    }
  </style>
</head>
<body>
  <header>
    <a href="index.php" class="logo">Flavorful</a>
    <nav>
      <a href="about.php">About</a>
      <a href="prices.php">Prices</a>
      <a href="socials.php">Socials</a>
      <a href="workers.php">Workers</a>

      <a href="register.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
      <a href="login.php" class="login-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Login</a>

      <div class="profile-icon" <?php echo !$authUser ? 'style="display:none;"' : ''; ?>>
        <!-- Link changed from balance.php to order.php/calculator.php -->
        <a href="order.php" class="profile-btn" title="Start Ordering"> 
            <span id="profile-initials"><?= htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span>
        </a>
        <form method="POST" style="display:inline;">
            <button type="submit" name="logout">Logout</button>
        </form>
      </div>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-text">
      <h2>Place Your Order</h2>
      <p>Select an option below to proceed with ordering, use our calculator, or arrange delivery.</p> 
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
        <span>Delivery</span>
        <i class='bx bx-car'></i>
      </a>
      <?php if (!$authUser): ?>
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