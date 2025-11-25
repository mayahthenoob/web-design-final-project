<?php
// ======================================
// delivery.php - Delivery/Checkout Page
// ======================================
?>
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
    header('Location: delivery.php');
    exit;
}

// Fetch products
try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY name ASC');
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
  <title>Flavorful - Delivery</title>
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
    .profile-btn { display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; border-radius: 50%; background: #f59e0b; color: #fff; text-decoration: none; font-weight: 700; }
    .hidden { display: none !important; }
    footer { text-align: center; padding: 20px; background: #fff; border-top: 1px solid #eee; font-size: 14px; color: #777; margin-top: auto; }

    .checkout-container { display: flex; max-width: 1200px; margin: 40px auto; padding: 20px; background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); flex-grow: 1; }

    .checkout-form-section { flex: 2; padding-right: 40px; border-right: 1px solid #eee; }
    .delivery-summary { flex: 1; padding-left: 40px; }

    .section-title { font-size: 1.8rem; font-weight: 600; margin-bottom: 30px; }
    .step-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 15px; margin-top: 20px; }

    .input-row { display: flex; gap: 20px; margin-bottom: 20px; }
    .input-field { flex: 1; }
    .input-field label { font-size: .8rem; font-weight: 600; }
    .input-field input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; }

    .checkout-button { width: 100%; padding: 15px; background: #f59e0b; border: none; border-radius: 10px; color: white; font-size: 1.1rem; font-weight: 600; cursor: pointer; }
    .checkout-button:hover { background: #e19a00; }

    .delivery-summary h3 { font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; }

    #deliverySearch { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; }

    .delivery-card { background: #fff; padding: 15px; border-radius: 10px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

    .delivery-add-btn { background: #f59e0b; color: #fff; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; }

    .delivery-item { display: flex; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
    .item-image-placeholder { width: 60px; height: 60px; background: #f1f1f1; border-radius: 10px; margin-right: 10px; text-align: center; line-height: 60px; font-size: 12px; }

    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: .95rem; }
    .summary-row.total { font-size: 1.3rem; font-weight: 700; }

    @media (max-width: 900px) { .checkout-container { flex-direction: column; } .checkout-form-section { padding-right: 0; border-right: none; border-bottom: 1px solid #eee; padding-bottom: 30px; } .delivery-summary { padding-left: 0; padding-top: 30px; } }
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
            <a href="balance.php" class="profile-btn"><span><?php echo htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span></a>
            <form method="POST" style="display:inline;"><button type="submit" name="logout" style="margin-left:10px;background:#f59e0b;color:white;border:none;padding:5px 10px;border-radius:4px;">Logout</button></form>
        </div>
    </div>
</header>

<main class="checkout-container">

    <section class="checkout-form-section">
        <h2 class="section-title">DELIVERY</h2>
        <form>
            <h3 class="step-title">1. Contact Information</h3>
            <div class="input-row">
                <div class="input-field">
                    <label>FIRST NAME</label>
                    <input type="text" required>
                </div>
                <div class="input-field">
                    <label>LAST NAME</label>
                    <input type="text" required>
                </div>
            </div>

            <div class="input-row">
                <div class="input-field">
                    <label>PHONE</label>
                    <input type="text" required>
                </div>
                <div class="input-field">
                    <label>EMAIL</label>
                    <input type="email" required>
                </div>
            </div>

            <h3 class="step-title">2. Address</h3>
            <div class="input-row">
                <div class="input-field">
                    <label>STREET ADDRESS</label>
                    <input type="text" required>
                </div>
            </div>

            <div class="input-row">
                <div class="input-field">
                    <label>CITY</label>
                    <input type="text" required>
                </div>
                <div class="input-field">
                    <label>PARISH</label>
                    <input type="text" required>
                </div>
            </div>

            <button type="submit" class="checkout-button">DELIVER</button>
        </form>
    </section>

    <aside class="delivery-summary">
        <h3>Delivery Summary</h3>
        <input id="deliverySearch" type="text" placeholder="Search Pennacool...">
        <div id="deliveryProductGrid"></div>
        <hr style="margin:25px 0">
        <div id="deliveryCartItems"></div>

        <div class="summary-details">
            <div class="summary-row"><span>SUBTOTAL</span><span id="deliverySubtotal">$0.00</span></div>
            <div class="summary-row"><span>DELIVERY FEE</span><span>$2.00</span></div>
            <hr>
            <div class="summary-row total"><span>TOTAL</span><span id="deliveryTotal">$2.00</span></div>
        </div>
    </aside>

</main>

<footer>&copy; 2025 Flavorful. | All rights reserved.</footer>

<script>
const deliveryProducts = <?php echo json_encode($products); ?>;
let deliveryCart = {};

function loadDeliveryProducts() {
    let grid = document.getElementById("deliveryProductGrid");
    grid.innerHTML = "";

    deliveryProducts.forEach(p => {
        grid.innerHTML += `
            <div class="delivery-card" data-name="${p.name}">
                <div>
                    <h4>${p.name} Pennacool</h4>
                    <p>$${parseFloat(p.price).toFixed(2)}</p>
                </div>
                <button class="delivery-add-btn" onclick="addDeliveryItem('${p.name}', ${p.price})">Add</button>
            </div>
        `;
    });
}
loadDeliveryProducts();

function addDeliveryItem(name, price) {
    if (!deliveryCart[name]) deliveryCart[name] = { qty: 0, price };
    deliveryCart[name].qty++;
    updateDeliveryCartUI();
}

function updateDeliveryCartUI() {
    let cartBox = document.getElementById("deliveryCartItems");
    cartBox.innerHTML = "";

    let subtotal = 0;

    Object.keys(deliveryCart).forEach(item => {
        let qty = deliveryCart[item].qty;
        let price = deliveryCart[item].price;
        let line = qty * price;
        subtotal += line;

        cartBox.innerHTML += `
            <div class="delivery-item">
                <div class="item-image-placeholder">${item}</div>
                <div>
                    <h4>${item} (x${qty})</h4>
                    <p style="font-size:12px;color:#666;">Qty: ${qty} | $${price.toFixed(2)} ea</p>
                    <p style="font-weight:600;">$${line.toFixed(2)}</p>
                </div>
            </div>
        `;
    });

    document.getElementById("deliverySubtotal").innerText = `$${subtotal.toFixed(2)}`;
    document.getElementById("deliveryTotal").innerText = `$${(subtotal + 2).toFixed(2)}`;
}

document.getElementById("deliverySearch").addEventListener("keyup", () => {
    let term = document.getElementById("deliverySearch").value.toLowerCase();
    document.querySelectorAll(".delivery-card").forEach(card => {
        card.style.display = card.dataset.name.toLowerCase().includes(term) ? "flex" : "none";
    });
});
</script>

</body>
</html>

<?php
// ======================================
// balance.php - User Account/Balance Page
// ======================================
?>
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

if (!$authUser) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Fetch user data
try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$authUser['id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    $balanceStmt = $pdo->prepare('SELECT * FROM account_balance WHERE user_id = ?');
    $balanceStmt->execute([$authUser['id']]);
    $balance = $balanceStmt->fetch(PDO::FETCH_ASSOC);

    $ordersStmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 10');
    $ordersStmt->execute([$authUser['id']]);
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $userData = $userData ?? [];
    $balance = [];
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - My Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; background-color: #fafafa; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
    
    header { display: flex; justify-content: space-between; align-items: center; padding: 20px 60px; background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 1000; flex-wrap: wrap; }
    header h1 .logo { font-size: 24px; font-weight: 700; text-decoration: none; color: #111; }
    .main-nav a { margin: 0 10px; text-decoration: none; color: #333; font-weight: 500; padding: 5px; transition: color 0.2s; }
    .main-nav a:hover { color: #f59e0b; }
    
    .account-links { display: flex; align-items: center; }
    .profile-btn { display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; border-radius: 50%; background-color: #f59e0b; color: white; font-weight: 700; text-decoration: none; }
    
    footer { text-align: center; padding: 20px; background: #fff; border-top: 1px solid #eee; font-size: 14px; color: #777; margin-top: auto; }

    .balance-container { max-width: 1000px; margin: 40px auto; padding: 20px; }

    .profile-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .profile-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .profile-info h2 { font-size: 28px; margin-bottom: 10px; }
    .profile-info p { color: #666; margin: 5px 0; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px; }
    .stat-card { background: #f9f9f9; padding: 20px; border-radius: 8px; text-align: center; }
    .stat-value { font-size: 28px; font-weight: 800; color: #f59e0b; }
    .stat-label { font-size: 14px; color: #666; margin-top: 5px; }

    .orders-section { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .orders-section h3 { font-size: 20px; margin-bottom: 20px; }

    .order-item { display: flex; justify-content: space-between; padding: 15px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 10px; }
    .order-info { flex: 1; }
    .order-date { color: #666; font-size: 14px; }
    .order-status { padding: 5px 15px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-confirmed { background: #dbeafe; color: #1e40af; }
    .status-delivered { background: #dcfce7; color: #166534; }

    @media (max-width: 768px) {
      header { padding: 15px 20px; }
      .profile-header { flex-direction: column; align-items: flex-start; }
      .stats-grid { grid-template-columns: 1fr; }
      .order-item { flex-direction: column; }
    }
  </style>
</head>
<body>
  <header>
    <h1><a href="index.php" class="logo">Flavorful</a></h1>
    <nav class="main-nav">
        <a href="index.php">Home</a>
        <a href="buy-now.php">Buy Now</a>
        <a href="prices.php">Prices</a>
        <a href="contact.php">Contact</a>
    </nav>
    <div class="account-links">
        <a href="balance.php" class="profile-btn"><span><?php echo htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span></a>
        <form method="POST" style="display:inline;">
            <button type="submit" name="logout" style="margin-left: 10px; padding: 5px 10px; background-color: #f59e0b; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Logout</button>
        </form>
    </div>
  </header>

  <div class="balance-container">
    <div class="profile-card">
      <div class="profile-header">
        <div class="profile-info">
          <h2>Welcome, <?php echo htmlspecialchars($userData['username'] ?? 'User'); ?>!</h2>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email'] ?? ''); ?></p>
          <p><strong>Phone:</strong> <?php echo htmlspecialchars($userData['phone'] ?? 'N/A'); ?></p>
          <p><strong>Location:</strong> <?php echo htmlspecialchars(($userData['address'] ?? 'N/A') . ', ' . ($userData['country'] ?? '')); ?></p>
        </div>
        <div class="profile-btn" style="font-size: 48px; width: 80px; height: 80px; line-height: 80px;">
          <?php echo htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?>
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-value">$<?php echo number_format($balance['total_spent'] ?? 0, 2); ?></div>
          <div class="stat-label">Total Spent</div>
        </div>
        <div class="stat-card">
          <div class="stat-value"><?php echo $balance['total_orders'] ?? 0; ?></div>
          <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">$<?php echo number_format($balance['balance'] ?? 0, 2); ?></div>
          <div class="stat-label">Account Balance</div>
        </div>
      </div>
    </div>

    <div class="orders-section">
      <h3>Recent Orders</h3>
      <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
          <div class="order-item">
            <div class="order-info">
              <p><strong>Order #<?php echo $order['id']; ?></strong></p>
              <p class="order-date"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
            </div>
            <div>
              <p style="text-align: right; margin-bottom: 5px;"><strong>$<?php echo number_format($order['total_amount'] + $order['delivery_fee'], 2); ?></strong></p>
              <span class="order-status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center; color: #999; padding: 30px;">No orders yet. <a href="buy-now.php" style="color: #f59e0b;">Start shopping now!</a></p>
      <?php endif; ?>
    </div>
  </div>

  <footer>&copy; 2025 Flavorful. | All rights reserved.</footer>
</body>
</html>