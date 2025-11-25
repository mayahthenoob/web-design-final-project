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
    header('Location: order.php');
    exit;
}

// Fetch all products from database
try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY name ASC');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

// Handle order submission
$orderSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    if (!$authUser) {
        header('Location: login.php');
        exit;
    }

    $cartData = isset($_POST['cart_data']) ? json_decode($_POST['cart_data'], true) : [];
    $totalAmount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
    $delivery = isset($_POST['delivery']) ? 1 : 0;

    if (!empty($cartData) && $totalAmount > 0) {
        try {
            $deliveryFee = $delivery ? 2.00 : 0;
            $finalTotal = $totalAmount + $deliveryFee;

            $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, delivery_fee, status) VALUES (?, ?, ?, ?)');
            $stmt->execute([$authUser['id'], $totalAmount, $deliveryFee, 'pending']);
            $orderId = $pdo->lastInsertId();

            // Insert order items
            foreach ($cartData as $item) {
                $productStmt = $pdo->prepare('SELECT id, price FROM products WHERE name = ?');
                $productStmt->execute([$item['name']]);
                $product = $productStmt->fetch(PDO::FETCH_ASSOC);

                if ($product) {
                    $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
                    $itemStmt->execute([$orderId, $product['id'], $item['qty'], $product['price']]);
                }
            }

            // Update account balance
            $balanceStmt = $pdo->prepare('SELECT id FROM account_balance WHERE user_id = ?');
            $balanceStmt->execute([$authUser['id']]);
            $balance = $balanceStmt->fetch(PDO::FETCH_ASSOC);

            if ($balance) {
                $updateStmt = $pdo->prepare('UPDATE account_balance SET total_spent = total_spent + ?, total_orders = total_orders + 1 WHERE user_id = ?');
                $updateStmt->execute([$finalTotal, $authUser['id']]);
            } else {
                $createStmt = $pdo->prepare('INSERT INTO account_balance (user_id, total_spent, total_orders) VALUES (?, ?, ?)');
                $createStmt->execute([$authUser['id'], $finalTotal, 1]);
            }

            $orderSuccess = true;
        } catch (PDOException $e) {
            $orderError = "Failed to process order: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Flavorful - Order</title>
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

    .content-container { display: grid; grid-template-columns: 250px 1fr 300px; gap: 20px; padding: 20px; max-width: 1400px; margin: auto; }
    .left-sidebar, .right-sidebar { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

    .search-box { display: flex; border: 1px solid #ddd; border-radius: 5px; overflow: hidden; margin-bottom: 30px; }
    .search-box input { flex: 1; padding: 10px; border: none; outline: none; }
    .icon-search { padding: 10px; background: #f5f5f5; }

    .side-menu h3 { font-size: 16px; color: #888; margin-bottom: 10px; }
    .side-menu ul { list-style: none; }
    .side-menu li { padding: 10px 0; cursor: pointer; display: flex; align-items: center; }
    .side-menu li:hover { background: #f9f9f9; }
    .side-menu li.active { background: #ffedcc; color: #f59e0b; font-weight: bold; border-right: 3px solid #f59e0b; }

    .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .product-card { background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; }
    .product-card:hover { transform: translateY(-5px); }
    .product-card img { width: 100%; height: 150px; object-fit: cover; }
    .price-add { display: flex; justify-content: space-between; padding: 10px 15px; }
    .add-btn { background: #f59e0b; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; }
    .add-btn:hover { background: #e6a100; }

    .order-item { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .order-title { text-align: center; margin-bottom: 20px; font-size: 20px; font-weight: 700; }
    .total-price { color: #f59e0b; font-weight: 700; }
    .confirm-order-btn { width: 100%; padding: 15px; background: #f59e0b; border: none; color: #fff; font-size: 16px; border-radius: 8px; cursor: pointer; }
    .confirm-order-btn:hover { background: #e6a100; }

    .popup { position: fixed; top: 20px; right: 20px; background: #30c030; color: white; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); font-weight: 600; opacity: 0; pointer-events: none; transition: opacity 0.4s ease; z-index: 2000; }
    .popup.show { opacity: 1; }

    @media (max-width: 1024px) { .content-container { grid-template-columns: 1fr; } .product-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .product-grid { grid-template-columns: 1fr; } }
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
            <a href="balance.php" class="profile-btn"><span id="profile-initials"><?php echo htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span></a>
            <form method="POST" style="display:inline;"><button type="submit" name="logout" style="margin-left: 10px; background:#f59e0b; color:#fff; padding:6px 12px; border-radius:4px; border:none;">Logout</button></form>
        </div>
    </div>
</header>

<main class="content-container">

    <aside class="left-sidebar">
        <div class="search-box">
            <input id="searchInput" type="text" placeholder="Search Flavors">
            <span class="icon-search">🔍</span>
        </div>

        <nav class="side-menu">
            <h3>FLAVORS</h3>
            <ul id="flavorList">
                <li data-name="Pineapple">🍍 Pineapple</li>
                <li data-name="Strawberry">🍓 Strawberry</li>
                <li data-name="Grape">🍇 Grape</li>
                <li data-name="Cherry">🍒 Cherry</li>
                <li data-name="Orange">🍊 Orange</li>
                <li data-name="Watermelon">🍉 Watermelon</li>
                <li data-name="Mango">🥭 Mango</li>
                <li data-name="Lemon">🍋 Lemon</li>
                <li data-name="Coconut">🥥 Coconut</li>
                <li data-name="Cola">🥤 Cola</li>
                <li data-name="Blue Raspberry">🔵 Blue Raspberry</li>
            </ul>
        </nav>
    </aside>

    <section class="main-content">
        <div class="product-grid" id="productGrid"></div>
    </section>

    <aside class="right-sidebar">
        <h2 class="order-title">MY ORDER</h2>

        <div id="orderItems"></div>

        <label style="display:flex; align-items:center; margin-top:15px;">
            <input type="checkbox" id="deliveryCheck"> Delivery (+$2.00)
        </label>

        <h3 style="margin-top:20px; display:flex; justify-content:space-between;">
            TOTAL: <span class="total-price" id="totalAmount">$0.00</span>
        </h3>

        <p style="font-size:12px; text-align:right;">VAT Included</p>

        <form id="orderForm" method="POST" style="display:none;">
            <input type="hidden" id="cartData" name="cart_data">
            <input type="hidden" id="totalData" name="total_amount">
            <input type="hidden" id="deliveryData" name="delivery">
            <button type="submit" name="submit_order" class="confirm-order-btn">Confirm Order</button>
        </form>

        <button class="confirm-order-btn" onclick="confirmOrder()">Confirm Order</button>
    </aside>
</main>

<div id="orderPopup" class="popup hidden">
    <p>✅ Order Sent Successfully!</p>
</div>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>

<script>
const products = <?php echo json_encode($products); ?>;
let cart = {};

function loadProducts() {
    const grid = document.getElementById("productGrid");
    grid.innerHTML = "";

    products.forEach(p => {
        grid.innerHTML += `
            <div class="product-card" data-name="${p.name}">
                <img src="https://placehold.co/300x150/f59e0b/fff?text=${encodeURIComponent(p.name)}">
                <h4>${p.name}</h4>
                <p>${p.name} Pennacool</p>
                <div class="price-add">
                    <span>$${parseFloat(p.price).toFixed(2)}</span>
                    <button class="add-btn" onclick="addToCart('${p.name}', ${p.price})">+</button>
                </div>
            </div>
        `;
    });
}
loadProducts();

function addToCart(name, price) {
    if (!cart[name]) cart[name] = { qty: 0, price };
    cart[name].qty++;
    updateCartUI();
}

function updateCartUI() {
    const container = document.getElementById("orderItems");
    container.innerHTML = "";

    let total = 0;

    Object.keys(cart).forEach(item => {
        const line = cart[item].qty * cart[item].price;
        total += line;

        container.innerHTML += `
            <div class="order-item">
                <p>${cart[item].qty} × ${item}</p>
                <p>$${line.toFixed(2)}</p>
            </div>
        `;
    });

    if (document.getElementById("deliveryCheck").checked) {
        total += 2;
    }

    document.getElementById("totalAmount").textContent = "$" + total.toFixed(2);
}

document.getElementById("deliveryCheck").addEventListener("change", updateCartUI);

document.getElementById("searchInput").addEventListener("keyup", () => {
    const searchText = document.getElementById("searchInput").value.toLowerCase();

    document.querySelectorAll("#flavorList li").forEach(li => {
        li.style.display = li.dataset.name.toLowerCase().includes(searchText) ? "block" : "none";
    });

    document.querySelectorAll(".product-card").forEach(card => {
        const name = card.dataset.name.toLowerCase();
        card.style.display = name.includes(searchText) ? "block" : "none";
    });
});

function confirmOrder() {
    <?php if (!$authUser): ?>
        alert('Please login to place an order');
        window.location.href = 'login.php';
    <?php else: ?>
        if (Object.keys(cart).length === 0) {
            alert('Please add items to your cart');
            return;
        }

        const cartArray = [];
        let total = 0;
        Object.keys(cart).forEach(item => {
            cartArray.push({name: item, qty: cart[item].qty});
            total += cart[item].qty * cart[item].price;
        });

        const delivery = document.getElementById("deliveryCheck").checked ? 1 : 0;
        if (delivery) total += 2;

        document.getElementById("cartData").value = JSON.stringify(cartArray);
        document.getElementById("totalData").value = total;
        document.getElementById("deliveryData").value = delivery;

        document.getElementById("orderForm").submit();
    <?php endif; ?>
}

<?php if ($orderSuccess): ?>
    const popup = document.getElementById("orderPopup");
    popup.classList.remove("hidden");
    popup.classList.add("show");
    setTimeout(() => {
        window.location.href = 'balance.php';
    }, 2500);
<?php endif; ?>
</script>

</body>
</html>