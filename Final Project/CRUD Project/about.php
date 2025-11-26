<?php
session_start();
$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: about.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - About</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* Global Styles */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background-color: #fff; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
    
    /* Header Styles (Consistent across files) */
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
      margin-bottom: 30px;
      font-size: 1.1em;
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

    /* Recent Section */
    .recent {
      padding: 60px;
      text-align: center;
    }
    .recent h3 {
      font-size: 2em;
      margin-bottom: 40px;
      color: #111;
    }
    .card-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
      height: 200px;
      object-fit: cover;
    }
    .card-content {
      padding: 20px;
    }
    .card-content span {
      display: block;
      color: #f59e0b;
      font-size: 0.85em;
      margin-bottom: 5px;
      font-weight: 600;
    }
    .card-content h4 {
      font-size: 1.25em;
      margin-bottom: 10px;
    }

    /* Footer Styles */
    footer {
        text-align: center;
        padding: 20px;
        background: #111;
        color: #eee;
        margin-top: auto;
    }

    /* Responsive adjustments */
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
        header .logo {
            margin-bottom: 10px;
        }
        nav {
            width: 100%;
            justify-content: space-between;
            margin-top: 10px;
        }
        .recent {
            padding: 40px 20px;
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
      <a href="socials.php">Socials</a>
      <a href="workers.php">Workers</a>
      <a href="buy-now.php">Order</a>

      <a href="register.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
      <a href="login.php" class="login-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Login</a>

      <div class="profile-icon" <?php echo !$authUser ? 'style="display:none;"' : ''; ?>>
        <!-- Link changed from balance.php to buy-now.php -->
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
      <h2>About Flavorful</h2>
      <p>This company was founded by the manager in 2019 they started off only selling in their neighbourhood, then to shops and when they expaneded the company a little in 2021 they started going to supermarkets and shops in various places around the island</p>
      <a href="workers.php" class="btn">Meet the Team</a>
    </div>
    <img src="https://placehold.co/400x300/f59e0b/ffffff?text=Manager" alt="Manager" onerror="this.onerror=null;this.src='https://placehold.co/400x300/f59e0b/ffffff?text=Manager';">
  </section>

  <section class="recent">
    <h3>Company Overview</h3>
    <div class="card-container">
      <div class="card">
        <img src="https://placehold.co/300x200/555/ffffff?text=Team" alt="Team">
        <div class="card-content">
          <span>Info</span>
          <h4>We consist of five main workers and three helpers</h4>
          <p>Located in Blaize St. Andrew, we deliver pennacools to shops, supermarkets and communities across the island.</p>
        </div>
      </div>
      <div class="card">
        <img src="https://placehold.co/300x200/f59e0b/ffffff?text=Quality" alt="Quality">
        <div class="card-content">
          <span>Quality Assurance</span>
          <h4>Fresh, local ingredients with strict sanitation protocols</h4>
          <p>We pride ourselves on offering a refreshing and high-quality product to all our customers.</p>
        </div>
      </div>
      <div class="card">
        <img src="https://placehold.co/300x200/d97706/ffffff?text=Delivery" alt="Delivery Van">
        <div class="card-content">
          <span>Logistics</span>
          <h4>Fast and Reliable Delivery</h4>
          <p>Utilizing our delivery network, we ensure your order reaches you promptly and in perfect condition.</p>
        </div>
      </div>
    </div>
  </section>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>