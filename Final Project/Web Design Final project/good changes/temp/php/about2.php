<?php
// ======================================
// about.php - About Page
// ======================================
?>
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
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful Website - About</title>
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
    
    .hidden { display: none !important; }
    footer { text-align: center; padding: 20px; background: #fff; border-top: 1px solid #eee; font-size: 14px; color: #777; margin-top: auto; }

    .hero { display: flex; align-items: center; justify-content: space-between; padding: 60px; background: #fafafa; }
    .hero-text { max-width: 45%; }
    .hero-text h2 { font-size: 62px; font-weight: 800; margin-bottom: 15px; }
    .hero-text p { font-size: 18px; color: #555; margin-bottom: 30px; }
    .hero-text .btn { display: inline-block; text-decoration: none; color: #fff; background: #f59e0b; padding: 10px 25px; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .hero-text .btn:hover { background: #e6a100; }
    .hero img { width: 45%; border-radius: 12px; }

    .recent { padding: 60px; text-align: center; }
    .recent h3 { font-size: 36px; font-weight: 800; margin-bottom: 40px; color: #000; }

    .card-container { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; }
    .card { background: #fff; border: 1px solid #eee; border-radius: 8px; width: 300px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); overflow: hidden; text-align: left; }
    .card img { width: 100%; height: 200px; object-fit: cover; }
    .card-content { padding: 20px; }
    .card-content span { display: block; color: #777; font-size: 14px; margin-bottom: 5px; }
    .card-content h4 { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
    .card-content p { font-size: 14px; color: #555; }

    @media (max-width: 768px) {
      header { flex-direction: column; align-items: flex-start; padding: 15px 20px; }
      header h1 { margin-bottom: 10px; }
      .main-nav { order: 2; display: flex; flex-wrap: wrap; margin-bottom: 10px; }
      .account-links { order: 1; margin-bottom: 10px; align-self: flex-end; }
      .hero { flex-direction: column; padding: 30px 20px; }
      .hero-text { max-width: 100%; }
      .hero img { width: 100%; margin-top: 20px; }
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
      <h2>Flavorful</h2>
      <p>This company was founded by the manager in 2019 they started off only selling in their neighbourhood, then to shops and when they expanded the company a little in 2021 they started going to supermarkets and shops in various places around the island</p>
      <a href="#" class="btn">Learn More</a>
    </div>
    <img src="https://placehold.co/400x300/f59e0b/ffffff?text=Manager" alt="Manager" onerror="this.onerror=null;this.src='https://placehold.co/400x300/f59e0b/ffffff?text=Manager';">
  </section>

  <section class="recent">
    <h3>Company Overview</h3>
    <div class="card-container">
      <div class="card">
        <img src="https://placehold.co/300x200/555/ffffff?text=Team" alt="Team" onerror="this.onerror=null;this.src='https://placehold.co/300x200/555/ffffff?text=Team';">
        <div class="card-content">
          <span>Info</span>
          <h4>We consist of five main workers and three helpers: three package, one mix, one drives and one sells to supermarket and shops</h4>
          <p>We are located in Blaize St. Andrew. We go around the island selling pennacools to shops, supermarkets and community</p> 
        </div>
      </div>

      <div class="card">
        <img src="https://placehold.co/300x200/f59e0b/ffffff?text=Quality" alt="Quality" onerror="this.onerror=null;this.src='https://placehold.co/300x200/f59e0b/ffffff?text=Quality';">
        <div class="card-content">
          <span>Quality Assurance</span>
          <h4>Our Pennacools are made with fresh, local ingredients and packaged with strict sanitation protocols.</h4>
          <p>We pride ourselves on offering a refreshing and high-quality product to all our customers.</p>
        </div>
      </div>
    </div>
  </section>

  <footer>
    &copy; 2025 Flavorful. | All rights reserved.
  </footer>
</body>
</html>

<?php
// ======================================
// calculator.php - Order Calculator
// ======================================
?>
<?php
session_start();

$authUser = isset($_SESSION['authUser']) ? $_SESSION['authUser'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: calculator.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Calculator</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background-color: #fafafa; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
    
    header { display: flex; justify-content: space-between; align-items: center; padding: 20px 60px; background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 1000; flex-wrap: wrap; }
    header h1 .logo { font-size: 24px; font-weight: 700; text-decoration: none; color: #111; }
    .main-nav a { margin: 0 10px; text-decoration: none; color: #333; font-weight: 500; padding: 5px; transition: color 0.2s; }
    .main-nav a:hover { color: #f59e0b; }
    
    .account-links { display: flex; align-items: center; }
    .account-links a { text-decoration: none; padding: 8px 15px; border-radius: 4px; font-weight: 700; margin-left: 15px; transition: background-color 0.2s; }
    
    .signup-link { color: #f59e0b; border: 1px solid #f59e0b; }
    .signup-link:hover { background-color: #f59e0b; color: white; }
    
    .login-link { color: white; background-color: #111; }
    .login-link:hover { background-color: #333; }

    .profile-icon { display: flex; align-items: center; margin-left: 15px; }
    .profile-btn { display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; border-radius: 50%; background-color: #f59e0b; color: white; font-weight: 700; text-decoration: none; }
    
    .hidden { display: none !important; }
    footer { text-align: center; padding: 20px; background: #fff; border-top: 1px solid #eee; font-size: 14px; color: #777; margin-top: auto; }

    .calculator-container { max-width: 600px; margin: 40px auto; padding: 20px; background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }

    .calc-title { text-align: center; font-size: 28px; font-weight: 800; margin-bottom: 30px; color: #111; }

    .calc-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 15px; }
    .calc-item-name { font-weight: 600; }
    .calc-item-price { color: #f59e0b; font-weight: 700; }

    .quantity-control { display: flex; align-items: center; gap: 10px; }
    .qty-btn { width: 30px; height: 30px; border: 1px solid #ddd; background: white; border-radius: 4px; cursor: pointer; font-weight: bold; }
    .qty-btn:hover { background: #f59e0b; color: white; border-color: #f59e0b; }
    .qty-display { min-width: 40px; text-align: center; }

    .summary { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-top: 30px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 16px; }
    .summary-row.total { font-size: 20px; font-weight: 800; border-top: 2px solid #f59e0b; padding-top: 10px; color: #f59e0b; }

    .checkout-btn { width: 100%; padding: 15px; background: #f59e0b; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 15px; }
    .checkout-btn:hover { background: #e6a100; }
  </style>
</head>
<body>
  <header>
    <h1><a href="index.php" class="logo">Flavorful</a></h1>
    <nav class="main-nav">
        <a href="index.php">Home</a>
        <a href="prices.php">Prices</a>
        <a href="buy-now.php">Buy</a>
        <a href="contact.php">Contact</a>
    </nav>
    <div class="account-links">
        <a href="register.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
        <a href="login.php" class="login-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Login</a>
        
        <div class="profile-icon" <?php echo !$authUser ? 'style="display:none;"' : ''; ?>>
            <a href="balance.php" class="profile-btn">
                <span><?php echo htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span>
            </a>
            <form method="POST" style="display:inline;">
                <button type="submit" name="logout" style="margin-left: 10px; padding: 5px 10px; background-color: #f59e0b; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Logout</button>
            </form>
        </div>
    </div>
  </header>

  <div class="calculator-container">
    <h2 class="calc-title">Order Calculator</h2>

    <div id="calculatorItems"></div>

    <div class="summary">
      <div class="summary-row">
        <span>Subtotal</span>
        <span id="subtotal">$0.00</span>
      </div>
      <div class="summary-row">
        <span>Delivery Fee</span>
        <span id="delivery">$0.00</span>
      </div>
      <div class="summary-row total">
        <span>TOTAL</span>
        <span id="total">$0.00</span>
      </div>

      <label style="display:flex; align-items:center; margin-top:15px;">
        <input type="checkbox" id="deliveryCheck"> Add Delivery (+$2.00)
      </label>
    </div>

    <button class="checkout-btn" onclick="proceedToOrder()">Proceed to Order</button>
  </div>

  <footer>&copy; 2025 Flavorful. | All rights reserved.</footer>

  <script>
    const items = [
      { name: 'Pineapple', price: 0.50 },
      { name: 'Strawberry', price: 0.50 },
      { name: 'Grape', price: 0.50 },
      { name: 'Cherry', price: 0.50 },
      { name: 'Orange', price: 0.50 },
      { name: 'Watermelon', price: 0.50 },
      { name: 'Mango', price: 0.50 },
      { name: 'Lemon', price: 0.50 },
      { name: 'Coconut', price: 0.50 },
      { name: 'Cola', price: 0.50 },
      { name: 'Blue Raspberry', price: 0.50 },
      { name: 'Small Packet', price: 5.00 },
      { name: 'Large Packet', price: 10.00 }
    ];

    let quantities = {};

    function renderCalculator() {
      const container = document.getElementById('calculatorItems');
      container.innerHTML = '';

      items.forEach(item => {
        const qty = quantities[item.name] || 0;
        container.innerHTML += `
          <div class="calc-item">
            <div>
              <div class="calc-item-name">${item.name}</div>
              <div class="calc-item-price">$${item.price.toFixed(2)}</div>
            </div>
            <div class="quantity-control">
              <button class="qty-btn" onclick="updateQty('${item.name}', -1)">-</button>
              <span class="qty-display">${qty}</span>
              <button class="qty-btn" onclick="updateQty('${item.name}', 1)">+</button>
            </div>
          </div>
        `;
      });
    }

    function updateQty(name, change) {
      quantities[name] = Math.max(0, (quantities[name] || 0) + change);
      updateSummary();
    }

    function updateSummary() {
      let subtotal = 0;
      Object.keys(quantities).forEach(name => {
        const item = items.find(i => i.name === name);
        subtotal += (quantities[name] || 0) * item.price;
      });

      const delivery = document.getElementById('deliveryCheck').checked ? 2.00 : 0;
      const total = subtotal + delivery;

      document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
      document.getElementById('delivery').textContent = '$' + delivery.toFixed(2);
      document.getElementById('total').textContent = '$' + total.toFixed(2);
    }

    document.getElementById('deliveryCheck').addEventListener('change', updateSummary);

    function proceedToOrder() {
      window.location.href = 'order.php';
    }

    renderCalculator();
    updateSummary();
  </script>
</body>
</html>