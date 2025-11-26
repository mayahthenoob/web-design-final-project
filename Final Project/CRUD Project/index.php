<?php
session_start();

// Check if user is logged in
$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
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
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #fff;
      color: #111;
      line-height: 1.6;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* HEADER STYLES */
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
    header .logo { 
      font-size: 24px; 
      font-weight: 700; 
      text-decoration: none; 
      color: #111; 
      flex-shrink: 0;
    }
    nav {
      display: flex;
      gap: 20px;
      align-items: center;
    }
    nav a {
      text-decoration: none;
      color: #111;
      font-weight: 600;
      padding: 5px 10px;
      border-radius: 4px;
      transition: background-color 0.3s;
    }
    nav a:hover {
      background-color: #f0f0f0;
    }

    .profile-icon {
        display: flex;
        align-items: center;
    }
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
        background-color: #e53e3e; /* Red for logout */
        color: white; 
        border: none; 
        border-radius: 4px; 
        cursor: pointer; 
        font-size: 14px;
        transition: background-color 0.3s;
    }
    .profile-icon button:hover {
        background-color: #c53030;
    }
    
    /* HERO SECTION */
    .hero {
        display: flex;
        align-items: center;
        justify-content: space-around;
        padding: 60px;
        background-color: #fef3c7; /* Light yellow background */
        min-height: 70vh;
    }

    .hero-text {
        max-width: 500px;
    }

    .hero-text h2 {
        font-size: 3em;
        margin-bottom: 20px;
        color: #d97706; /* Darker orange */
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

    /* FOOTER STYLES */
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
        header .logo {
            margin-bottom: 10px;
        }
        nav {
            width: 100%;
            justify-content: space-between;
            margin-top: 10px;
        }
        .hero {
            flex-direction: column;
            padding: 40px 20px;
            min-height: auto;
        }
        .hero-text {
            text-align: center;
            margin-bottom: 30px;
            max-width: 100%;
        }
        .hero-text h2 {
            font-size: 2.2em;
        }
        .hero img {
            max-width: 90%;
            width: 100%;
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
      <a href="socials.php">Socials</a>
      <a href="workers.php">Workers</a>
      <a href="buy-now.php">Order</a>

      <a href="register.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
      <a href="login.php" class="login-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Login</a>

      <div class="profile-icon" <?php echo !$authUser ? 'style="display:none;"' : ''; ?>>
        <!-- Removed link to balance.php -->
        <a href="buy-now.php" class="profile-btn" id="profile-btn" title="View Order Options"> 
          <span id="profile-initials"><?php echo htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span>
        </a>
        <form method="POST" style="display:inline;">
          <button type="submit" name="logout">Logout</button>
        </form>
      </div>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-text">
      <h2>Introduction</h2>
      <p>Flavorful is a company you got to get pennacool from we got good deals that satisfy customers, we got good reviews on our product and the most important thing is we are here for you. We provide only the best quality and flavor!</p>
      <a href="buy-now.php" class="btn">Order Now</a>
    </div>
    <img src="https://placehold.co/400x300/f59e0b/ffffff?text=Flavorful+Image" alt="A collection of colorful pennacool drinks" onerror="this.src='https://placehold.co/400x300/f59e0b/ffffff?text=Flavorful+Image';">
  </section>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>