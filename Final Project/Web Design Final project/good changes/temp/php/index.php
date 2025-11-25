<?php
session_start();

// Initialize database connection
$dsn = "mysql:host=sql300.infinityfree.com;dbname=dbname"; //might have to change these to what th website gives
$username = "noels";
$password = "noelbest2025"

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Homepage</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  
  <style id="FLAVORFUL_GLOBAL_STYLE">
    /* GLOBAL RESET */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #fff;
        color: #111;
        line-height: 1.6;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    /* HEADER & NAVIGATION STYLES (Standardized) */
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 60px;
        background: #fff;
        border-bottom: 1px solid #eee;
        position: sticky;
        top: 0;
        z-index: 1000;
        flex-wrap: wrap; 
    }

    header h1 .logo {
        font-size: 24px;
        font-weight: 700;
        text-decoration: none;
        color: #111;
    }

    .main-nav a {
        margin: 0 10px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        padding: 5px;
        transition: color 0.2s;
    }

    .main-nav a:hover {
        color: #f59e0b; /* Using a warm color for hover */
    }
    
    .account-links {
        display: flex;
        align-items: center;
    }

    .account-links a {
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 4px;
        font-weight: 700;
        margin-left: 15px;
        transition: background-color 0.2s, color 0.2s;
    }
    
    .signup-link {
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }
    .signup-link:hover {
        background-color: #f59e0b;
        color: white;
    }
    
    .login-link {
        color: white;
        background-color: #111;
    }
    .login-link:hover {
        background-color: #333;
    }

    /* Profile Icon Styles */
    .profile-icon {
        display: flex;
        align-items: center;
        margin-left: 15px;
    }
    
    .profile-btn {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f59e0b;
        color: white;
        font-weight: 700;
        text-decoration: none;
        margin: 0;
        padding: 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    
    .profile-btn span {
        font-size: 16px;
    }
    
    .hidden {
        display: none !important;
    }

    /* Standard Footer Style */
    footer {
        text-align: center;
        padding: 20px;
        background: #fff;
        border-top: 1px solid #eee;
        font-size: 14px;
        color: #777;
        margin-top: auto; 
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
        header {
            padding: 15px 30px;
        }
        .main-nav a {
            margin: 0 5px;
            font-size: 14px;
        }
    }

    @media (max-width: 768px) {
        header {
            flex-direction: column;
            align-items: flex-start;
            padding: 15px 20px;
        }
        header h1 {
            margin-bottom: 10px;
        }
        .main-nav {
            order: 2; 
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        .account-links {
            order: 1; 
            margin-bottom: 10px;
            align-self: flex-end;
        }
    }

    /* Index content styles */
    .hero {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 60px;
      background: #fafafa;
    }

    .hero-text {
      max-width: 45%;
    }

    .hero-text h2 {
      font-size: 62px;
      font-weight: 800;
      margin-bottom: 15px;
    }

    .hero-text p {
      font-size: 18px;
      color: #555;
      margin-bottom: 30px;
    }

    .hero-text .btn {
      display: inline-block;
      text-decoration: none;
      color: #fff;
      background: #f59e0b;
      padding: 10px 25px;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
    }

    .hero-text .btn:hover {
      background: #e6a100;
    }

    .hero img {
      width: 45%;
      border-radius: 12px;
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
        <a href="sign-up.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
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
      <h2>Introduction</h2>
      <p>Flavorful is a company you got to get pennacool from we got good deals that satisfy customers, we got good reviews on our product and the most important thing is we are here for you. We provide only the best quality and flavor!</p>
      <a href="buy-now.php" class="btn">Order Now</a>
    </div>
    <img src="https://placehold.co/400x300/f59e0b/ffffff?text=Flavorful+Image" alt="Project Image" onerror="this.onerror=null;this.src='https://placehold.co/400x300/f59e0b/ffffff?text=Flavorful+Image';">
  </section>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
</body>
</html>