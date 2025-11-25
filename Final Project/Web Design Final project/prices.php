<?php
session_start();

$host = 'localhost';
$db = 'flavorful';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: prices.php');
    exit;
}

// Fetch all products from database
try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY price ASC, name ASC');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Prices</title>
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

    .prices-section { padding: 60px; text-align: center; background-image: url('background.jpeg'); background-size: cover; background-position: center; }
    .prices-section h2 { font-size: 48px; font-weight: 800; margin-bottom: 10px; color: #111; }
    .prices-section p.sub-text { font-size: 18px; color: #555; margin-bottom: 40px; }

    .card-container { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; max-width: 1200px; margin: 0 auto; }

    .card { background: white; border-radius: 12px; width: 250px; box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1); overflow: hidden; text-align: left; transition: transform 0.3s; }
    .card:hover { transform: translateY(-5px); }

    .card img { width: 100%; height: 180px; object-fit: cover; }

    .card-content { padding: 20px; }
    .card-content span { display: block; color: #f59e0b; font-size: 16px; font-weight: 600; margin-bottom: 5px; }
    .card-content h4 { font-size: 24px; font-weight: 800; margin-bottom: 10px; }
    .card-content p { font-size: 14px; color: #555; }
    
    @media (max-width: 600px) {
        .prices-section { padding: 30px 10px; }
        .card { width: 100%; max-width: 300px; }
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

  <section class="prices-section">
    <h2>Our Product Prices</h2>
    <p class="sub-text">Delicious pennacools in various flavors and sizes.</p>
    <div class="card-container">
      <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <div class="card">
            <img src="https://placehold.co/300x180/f59e0b/ffffff?text=<?php echo urlencode($product['name']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <div class="card-content">
              <span><?php echo htmlspecialchars($product['flavor'] ?? $product['name']); ?></span>
              <h4>$<?php echo number_format($product['price'], 2); ?></h4>
              <p><?php echo htmlspecialchars($product['description'] ?? 'Premium pennacool flavor'); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No products available at the moment.</p>
      <?php endif; ?>
    </div>
  </section>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>