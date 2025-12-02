<?php
session_start();

$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: workers.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Workers</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

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

    .workers-container { padding: 50px 20px; text-align: center; }
    .workers-container h2 { font-size: 40px; font-weight: 800; margin-bottom: 50px; color: #000; }

    .card-grid { display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; }
    .card { background-color: #f0f0f0; border-radius: 12px; padding: 20px; width: 300px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }

    .card img { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 15px; }
    .card h4 { font-size: 20px; font-weight: 700; margin-bottom: 5px; color: #111; }
    .card span { display: block; color: #f59e0b; font-weight: 600; margin-bottom: 10px; }
    .card p { font-size: 14px; color: #555; margin-bottom: 15px; }

    .socials a { color: #333; font-size: 20px; margin: 0 8px; transition: color 0.3s; }
    .socials a:hover { color: #f59e0b; }
    
    @media (max-width: 600px) {
      .card-grid { flex-direction: column; align-items: center; }
      .card { width: 90%; }
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

  <section class="workers-container">
    <h2>Our Dedicated Team</h2>
    <div class="card-grid">
      <div class="card">
        <img src="https://placehold.co/300x200/f59e0b/ffffff?text=Manager" alt="Manager">
        <h4>Manager</h4>
        <span>CEO & Founder</span>
        <p>Founded the company in 2019 and oversees all operations, including product development and major deliveries.</p>
        <div class="socials">
          <a href="#"><i class='bx bxl-whatsapp'></i></a>
          <a href="#"><i class='bx bxl-facebook'></i></a>
          <a href="#"><i class='bx bxl-instagram'></i></a>
          <a href="#"><i class='bx bxl-twitter'></i></a>
        </div>
      </div>

      <div class="card">
        <img src="https://placehold.co/300x200/f59e0b/ffffff?text=Akim" alt="Akim">
        <h4>Akim</h4>
        <span>Lead Packager</span>
        <p>Packages the pennacools and organizes them into $5.00 and $10.00 packets.</p>
        <div class="socials">
          <a href="#"><i class='bx bxl-whatsapp'></i></a>
          <a href="#"><i class='bx bxl-facebook'></i></a>
          <a href="#"><i class='bx bxl-instagram'></i></a>
          <a href="#"><i class='bx bxl-twitter'></i></a>
        </div>
      </div>

      <div class="card">
        <img src="https://placehold.co/300x200/f59e0b/ffffff?text=Gabriel" alt="Gabriel">
        <h4>Gabriel</h4>
        <span>Packager</span>
        <p>Works alongside Akim in packaging pennacools and ensuring quality control.</p>
        <div class="socials">
          <a href="#"><i class='bx bxl-whatsapp'></i></a>
          <a href="#"><i class='bx bxl-facebook'></i></a>
          <a href="#"><i class='bx bxl-instagram'></i></a>
          <a href="#"><i class='bx bxl-twitter'></i></a>
        </div>
      </div>

      <div class="card">
        <img src="https://placehold.co/300x200/f59e0b/ffffff?text=Allana" alt="Allana"> 
        <h4>Allana</h4>
        <span>Mixer</span>
        <p>Responsible for mixing flavors and ensuring product consistency.</p>
        <div class="socials">
          <a href="#"><i class='bx bxl-whatsapp'></i></a>
          <a href="#"><i class='bx bxl-facebook'></i></a>
          <a href="#"><i class='bx bxl-instagram'></i></a>
          <a href="#"><i class='bx bxl-twitter'></i></a>
        </div>
      </div>

      <div class="card">
        <img src="https://placehold.co/300x200/f59e0b/ffffff?text=Ezekiel" alt="Ezekiel">
        <h4>Ezekiel</h4>
        <span>Sales & Delivery</span>
        <p>Handles the bulk of sales and deliveries to supermarkets and local shops.</p>
        <div class="socials">
          <a href="#"><i class='bx bxl-whatsapp'></i></a>
          <a href="#"><i class='bx bxl-facebook'></i></a>
          <a href="#"><i class='bx bxl-instagram'></i></a>
          <a href="#"><i class='bx bxl-twitter'></i></a>
        </div>
      </div>
    </div>
  </section>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>