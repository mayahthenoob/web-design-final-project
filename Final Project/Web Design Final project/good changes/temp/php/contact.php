<?php
session_start();

$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : '';
$message = isset($_GET['message']) ? $_GET['message'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: contact.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful Website - Contact</title>
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

    .contact-section { padding: 60px 20px; }
    .contact-section h2 { font-size: 48px; font-weight: 800; text-align: center; margin-bottom: 10px; }
    .contact-section p.sub-text { font-size: 18px; color: #777; text-align: center; margin-bottom: 40px; }

    .contact-container { display: flex; max-width: 1200px; margin: 0 auto; background: #f9f9f9; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); overflow: hidden; }

    .contact-info { flex: 1; padding: 40px; background: #f59e0b; color: white; }
    .info-item { display: flex; align-items: center; margin-bottom: 30px; }
    .info-icon { font-size: 24px; margin-right: 20px; }
    .info-text h3 { font-size: 20px; margin-bottom: 5px; }
    .info-text p { font-size: 16px; color: #eee; }

    .contact-form { flex: 1; padding: 40px; background: white; }
    .contact-form h2 { font-size: 32px; margin-bottom: 25px; color: #111; }

    .contact-form input[type="text"],
    .contact-form input[type="email"],
    .contact-form textarea { width: 100%; padding: 15px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; outline: none; transition: border-color 0.3s; }

    .contact-form input:focus,
    .contact-form textarea:focus { border-color: #f59e0b; }

    .contact-form textarea { resize: vertical; }

    .contact-form button[type="submit"] { background-color: #f59e0b; color: white; padding: 15px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 18px; font-weight: 600; transition: background-color 0.3s; width: 100%; }

    .contact-form button[type="submit"]:hover { background-color: #e6a100; }

    .alert { padding: 12px; border-radius: 4px; margin-bottom: 20px; text-align: center; font-weight: 600; }
    .alert-success { background: #d1fae5; color: #059669; }
    .alert-error { background: #fee2e2; color: #dc2626; }

    @media (max-width: 900px) {
      .contact-container { flex-direction: column; }
      .contact-info { order: 2; }
      .contact-form { order: 1; }
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

  <section class="contact-section">
    <div class="container">
      <h2>Get In Touch</h2>
      <p class="sub-text">We'd love to hear from you! Send us a message or find our contact details below.</p>

      <?php if ($status === 'success'): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
      <?php elseif ($status === 'error'): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <div class="contact-container">
        <div class="contact-info">
          <div class="info-item">
            <div class="info-icon">📍</div>
            <div class="info-text">
              <h3>Address</h3>
              <p>Blaize,<br>St.Andrew,<br>Grenada</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon">📞</div>
            <div class="info-text">
              <h3>Phone</h3>
              <p>1 (473) 456-2535</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon">✉️</div>
            <div class="info-text">
              <h3>Email</h3>
              <p>support@flavorful.com</p>
            </div>
          </div>
        </div>

        <div class="contact-form">
          <form action="contact_form_handler.php" method="POST">
            <h2>Send Us a Message</h2>
            <input type="text" name="fullname" placeholder="Name or Username" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <textarea name="message" placeholder="Type your Message..." rows="6" required></textarea>
            <button type="submit">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>