<?php
session_start();

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
    // $pdo = new PDO($dsn, $user, $pass, $options); // Connection established but not strictly needed for this page.
} catch (PDOException $e) {
    error_log("DB connection check failed: " . $e->getMessage());
}

// Check logged-in user session
$authUser = $_SESSION['authUser'] ?? null;

// Logout handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: socials.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Socials</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

  <style id="FLAVORFUL_GLOBAL_STYLE">
    /* Global Styles */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background: #fff; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }

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
        display: flex;
        align-items: center;
        justify-content: space-around;
        padding: 60px;
        background-color: #fef3c7;
        gap: 40px;
        margin-bottom: 40px;
    }
    .hero-text {
        max-width: 500px;
    }
    .hero-text h2 {
        font-size: 2.5em;
        margin-bottom: 20px;
        color: #d97706;
    }
    .hero-text p {
        font-size: 1.1em;
        margin-bottom: 30px;
    }
    .hero img {
        max-width: 400px;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    /* Recent Section (Social Cards) */
    .recent {
      padding: 0 60px 60px;
      text-align: center;
    }
    .recent h3 {
      font-size: 2em;
      margin-bottom: 40px;
      color: #111;
    }
    .card-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
      max-width: 1200px;
      margin: 0 auto;
    }
    .card {
      background: #f9f9f9;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      text-align: left;
      transition: transform 0.3s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }
    .card-content {
      padding: 20px;
    }
    .card-content h4 {
      font-size: 1.25em;
      margin-bottom: 10px;
    }
    .card-content a {
      color: #f59e0b;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }
    .card-content a:hover {
        color: #d97706;
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
    @media (max-width: 900px) {
        .hero {
            flex-direction: column;
            text-align: center;
        }
        .hero img {
            max-width: 90%;
            margin-top: 30px;
        }
    }

    @media (max-width: 768px) {
        header {
            padding: 15px 20px;
            flex-direction: column;
            align-items: flex-start;
        }
        header .logo { margin-bottom: 10px; }
        nav { width: 100%; justify-content: space-between; margin-top: 10px; }
        .recent {
            padding: 0 20px 40px;
        }
        .card-container {
            grid-template-columns: 1fr;
        }
    }
  </style>
</head>
<body>
  <header>
    <a href="index.php" class="logo">Flavorful</a>
    <nav>
      <a href="about.php">About</a>
      <a href="prices.php">Prices</a>
      <a href="socials.php" style="background-color: #f0f0f0;">Socials</a>
      <a href="workers.php">Workers</a>
      <a href="buy-now.php">Order</a>

      <a href="register.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
      <a href="login.php" class="login-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Login</a>

      <div class="profile-icon" <?php echo !$authUser ? 'style="display:none;"' : ''; ?>>
        <!-- Removed link to balance.php -->
        <a href="buy-now.php" class="profile-btn" title="View Order Options"> 
            <span><?= htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span>
        </a>
        <form method="POST" style="display:inline;">
            <button type="submit" name="logout">Logout</button>
        </form>
      </div>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-text">
      <h2>Visit Our Social Media Page</h2>
      <p>Follow us for updates on deals, communication, deliveries, and schedule announcements.</p>
    </div>

    <img src="https://placehold.co/400x300/f59e0b/ffffff?text=Socials" alt="Flavorful Social Media" 
         onerror="this.src='https://placehold.co/400x300/f59e0b/ffffff?text=Socials';">
  </section>

  <section class="recent">
    <h3>Connect With Us</h3>
    <div class="card-container">

      <div class="card">
        <img src="https://placehold.co/300x200/3b5998/ffffff?text=Facebook" alt="Facebook Page">
        <div class="card-content">
          <h4>Facebook</h4>
          <p>Find us on the world's largest social network for all official announcements.</p>
          <a href="https://facebook.com/flavorful" target="_blank">Flavorful Grenada</a>
        </div>
      </div>

      <div class="card">
        <img src="https://placehold.co/300x200/e1306c/ffffff?text=Instagram" alt="Instagram Page">
        <div class="card-content">
          <h4>Instagram</h4>
          <p>See our latest flavors and beautiful product photography.</p>
          <a href="https://instagram.com/flavorful" target="_blank">@FlavorfulGD</a>
        </div>
      </div>

      <div class="card">
        <img src="https://placehold.co/300x200/25d366/ffffff?text=Whatsapp" alt="WhatsApp Icon">
        <div class="card-content">
          <h4>WhatsApp</h4>
          <p>Quickly contact us for support or bulk order inquiries.</p>
          <a href="https://wa.me/14734562535" target="_blank">Message Us</a>
        </div>
      </div>
      
    </div>
  </section>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>