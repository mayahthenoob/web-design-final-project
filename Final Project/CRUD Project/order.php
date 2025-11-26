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
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

$authUser = $_SESSION['authUser'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: order.php');
    exit;
}

// Fetch products
try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY name ASC');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    error_log("Product fetch failed: " . $e->getMessage());
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
    $delivery = isset($_POST['delivery_data']) ? intval($_POST['delivery_data']) : 0;
    $deliveryFee = $delivery ? 2.00 : 0.00;
    
    // Basic validation
    if ($totalAmount <= 0 || empty($cartData)) {
        // Handle error gracefully - this should be prevented client-side too
        error_log("Invalid order attempt by user " . $authUser['id']);
        $orderSuccess = false;
    } else {
        try {
            // Start Transaction
            $pdo->beginTransaction();

            // 1. Insert Order
            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, total_amount, delivery_fee, delivery_address, phone, status) 
                VALUES (:user_id, :total_amount, :delivery_fee, :delivery_address, :phone, 'pending')
            ");
            $stmt->execute([
                'user_id' => $authUser['id'],
                'total_amount' => $totalAmount,
                'delivery_fee' => $deliveryFee,
                'delivery_address' => $authUser['address'] ?? 'N/A', // Use stored address
                'phone' => $authUser['phone'] ?? 'N/A' // Use stored phone
            ]);
            $orderId = $pdo->lastInsertId();

            // 2. Insert Order Items
            $itemStmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_name, quantity, price) 
                VALUES (:order_id, :product_name, :quantity, :price)
            ");
            
            foreach ($cartData as $item) {
                // Find the actual price from the products list to prevent tampering
                $productPrice = 0;
                foreach ($products as $p) {
                    if ($p['name'] === $item['name']) {
                        $productPrice = $p['price'];
                        break;
                    }
                }

                if ($productPrice > 0) {
                    $itemStmt->execute([
                        'order_id' => $orderId,
                        'product_name' => $item['name'],
                        'quantity' => $item['qty'],
                        'price' => $productPrice // Use database price
                    ]);
                }
            }

            // 3. Commit Transaction
            $pdo->commit();
            $orderSuccess = true;

        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Order failed (DB error): " . $e->getMessage());
            $orderSuccess = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flavorful - Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* Global Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: #fff; color: #111; line-height: 1.6; display: flex; flex-direction: column; min-height: 100vh; }
        
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

        /* Main Order Layout */
        .order-container {
            display: flex;
            padding: 40px 60px;
            max-width: 1400px;
            margin: 0 auto;
            gap: 30px;
            flex-grow: 1;
        }

        /* Product List */
        .product-section {
            flex: 2;
        }
        .product-section h2 {
            font-size: 2em;
            color: #d97706;
            margin-bottom: 20px;
        }
        .search-box {
            margin-bottom: 20px;
            position: relative;
        }
        #productSearch {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: relative;
        }
        .product-card img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
            object-fit: cover;
            background: #fef3c7;
        }
        .product-card h4 {
            font-size: 1.2em;
            margin-bottom: 5px;
        }
        .product-card .price {
            color: #10b981;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .product-card button {
            background-color: #f59e0b;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: 600;
        }
        .product-card button:hover {
            background-color: #d97706;
        }

        /* Cart Summary */
        .cart-section {
            flex: 1;
            padding: 20px;
            border-radius: 8px;
            background: #f9f9f9;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            min-width: 300px;
            position: sticky;
            top: 100px;
            align-self: flex-start;
        }
        .cart-section h2 {
            font-size: 1.8em;
            color: #d97706;
            margin-bottom: 20px;
        }
        #cartItems {
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
            min-height: 100px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dotted #ccc;
        }
        .item-info {
            flex-grow: 1;
        }
        .item-info h4 {
            font-size: 1em;
        }
        .item-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .item-actions button {
            background: #eee;
            border: 1px solid #ccc;
            width: 25px;
            height: 25px;
            border-radius: 4px;
            cursor: pointer;
        }
        .item-actions span {
            font-weight: 600;
            min-width: 20px;
            text-align: center;
        }

        .totals-box {
            border-top: 2px solid #ddd;
            padding-top: 15px;
            margin-top: 15px;
        }
        .totals-box div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .totals-box .total {
            font-size: 1.2em;
            font-weight: 700;
            color: #111;
            border-top: 1px dashed #ccc;
            padding-top: 8px;
            margin-top: 8px;
        }
        .delivery-option {
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fef3c7;
            padding: 10px;
            border-radius: 6px;
        }

        .confirm-btn {
            width: 100%;
            background-color: #10b981;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 15px;
        }
        .confirm-btn:hover {
            background-color: #059669;
        }

        /* Footer Styles */
        footer {
            text-align: center;
            padding: 20px;
            background: #111;
            color: #eee;
            margin-top: auto;
        }
        
        /* Message Modal (for replacing alert()) */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none; /* Hidden by default */
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            max-width: 300px;
        }
        .modal-content button {
            background-color: #f59e0b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            margin-top: 15px;
            cursor: pointer;
        }

        /* Order Success Popup (replaces the old inline script logic) */
        .order-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 3000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
        }
        .order-popup.show {
            opacity: 1;
            visibility: visible;
        }
        .popup-content {
            background: #10b981;
            color: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.4);
        }
        .popup-content h3 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        .popup-content p {
            margin-bottom: 20px;
        }

        /* RESPONSIVENESS */
        @media (max-width: 1024px) {
            .order-container {
                flex-direction: column;
                padding: 40px 20px;
            }
            .cart-section {
                position: static;
                width: 100%;
                margin-top: 30px;
            }
        }
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-direction: column;
                align-items: flex-start;
            }
            nav { width: 100%; justify-content: space-between; margin-top: 10px; }
            .product-grid {
                grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
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
            <a href="buy-now.php" style="background-color: #f0f0f0;">Order</a>

            <a href="register.php" class="signup-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Sign Up</a>
            <a href="login.php" class="login-link" <?php echo $authUser ? 'style="display:none;"' : ''; ?>>Login</a>

            <div class="profile-icon" <?php echo !$authUser ? 'style="display:none;"' : ''; ?>>
                <a href="buy-now.php" class="profile-btn" title="View Order Options"> 
                    <span><?= htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span>
                </a>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="logout">Logout</button>
                </form>
            </div>
        </nav>
    </header>

    <main class="order-container">
        <!-- Product List Section -->
        <section class="product-section">
            <h2>Our Delicious Pennacools</h2>
            
            <div class="search-box">
                <i class='bx bx-search'></i>
                <input type="text" id="productSearch" placeholder="Search products...">
            </div>

            <div class="product-grid" id="productGrid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-name="<?= htmlspecialchars(strtolower($product['name'])); ?>">
                        <img src="https://placehold.co/80x80/f59e0b/ffffff?text=<?= urlencode(substr($product['name'], 0, 3)); ?>" 
                             alt="<?= htmlspecialchars($product['name']); ?>"
                             onerror="this.src='https://placehold.co/80x80/f59e0b/ffffff?text=<?= urlencode(substr($product['name'], 0, 3)); ?>';">
                        <h4><?= htmlspecialchars($product['name']); ?></h4>
                        <p class="price">$<?= number_format($product['price'], 2); ?></p>
                        <p style="font-size:0.9em; color:#777; margin-bottom:10px;"><?= htmlspecialchars(substr($product['description'], 0, 50)); ?>...</p>
                        <button onclick="addToCart('<?= htmlspecialchars($product['name']); ?>', <?= $product['price']; ?>)">Add to Cart</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Cart Summary Section -->
        <section class="cart-section">
            <h2>Your Order Cart</h2>
            <div id="cartItems">
                <p style="color:#666; font-style:italic;">Cart is empty. Start adding items!</p>
            </div>
            
            <div class="totals-box">
                <div><span>Subtotal:</span> <span id="subtotal">$0.00</span></div>
                <div id="deliveryFeeLine" style="display:none;"><span>Delivery Fee:</span> <span>$2.00</span></div>
                <div class="total"><span>Total:</span> <span id="total">$0.00</span></div>
            </div>

            <div class="delivery-option">
                <input type="checkbox" id="deliveryCheck">
                <label for="deliveryCheck">Request Home Delivery ($2.00 Fee)</label>
            </div>

            <form id="orderForm" method="POST" action="order.php" style="display: none;">
                <input type="hidden" name="cart_data" id="cartData">
                <input type="hidden" name="total_amount" id="totalData">
                <input type="hidden" name="delivery_data" id="deliveryData">
                <input type="hidden" name="submit_order" value="1">
            </form>

            <button class="confirm-btn" onclick="confirmOrder()">Confirm & Checkout</button>
        </section>
    </main>

    <footer>
        &copy; 2025 Flavorful. | All rights reserved.
    </footer>
    
    <!-- Order Success Popup -->
    <div id="orderPopup" class="order-popup">
        <div class="popup-content">
            <h3>Order Placed Successfully!</h3>
            <p>Your order is pending confirmation. You will be redirected shortly.</p>
        </div>
    </div>
    
    <!-- Message Modal (for replacing alert()) -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <p id="modalMessage"></p>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>

    <script>
        // Data structure to hold the cart: { 'Product Name': { qty: N, price: P } }
        let cart = {};
        const DELIVERY_FEE = 2.00;
        const authUser = <?php echo json_encode($authUser); ?>;
        
        // --- Modal/Alert Replacement Functions ---
        function showModal(message) {
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('messageModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
        }

        // --- Cart Functions ---
        function calculateTotals() {
            let subtotal = 0;
            Object.keys(cart).forEach(item => {
                subtotal += cart[item].qty * cart[item].price;
            });
            
            const isDelivery = document.getElementById("deliveryCheck").checked;
            const total = subtotal + (isDelivery ? DELIVERY_FEE : 0);

            document.getElementById("subtotal").innerText = `$${subtotal.toFixed(2)}`;
            document.getElementById("total").innerText = `$${total.toFixed(2)}`;
            document.getElementById("deliveryFeeLine").style.display = isDelivery ? 'flex' : 'none';
        }

        function updateCartUI() {
            const cartBox = document.getElementById("cartItems");
            cartBox.innerHTML = "";

            if (Object.keys(cart).length === 0) {
                cartBox.innerHTML = '<p style="color:#666; font-style:italic;">Cart is empty. Start adding items!</p>';
            } else {
                Object.keys(cart).forEach(itemName => {
                    const item = cart[itemName];
                    const lineTotal = item.qty * item.price;
                    cartBox.innerHTML += `
                        <div class="cart-item">
                            <div class="item-info">
                                <h4>${itemName}</h4>
                                <p style="font-size:0.9em; color:#777;">$${item.price.toFixed(2)} ea | Total: $${lineTotal.toFixed(2)}</p>
                            </div>
                            <div class="item-actions">
                                <button onclick="changeQty('${itemName}', -1)">-</button>
                                <span>${item.qty}</span>
                                <button onclick="changeQty('${itemName}', 1)">+</button>
                            </div>
                        </div>
                    `;
                });
            }
            calculateTotals();
        }

        function addToCart(name, price) {
            if (!cart[name]) {
                cart[name] = { qty: 0, price: price };
            }
            cart[name].qty += 1;
            updateCartUI();
        }

        function changeQty(name, delta) {
            if (!cart[name]) return;

            cart[name].qty += delta;

            if (cart[name].qty <= 0) {
                delete cart[name];
            }
            updateCartUI();
        }

        function confirmOrder() {
            if (!authUser) {
                showModal('Please login to place an order.');
                // window.location.href = 'login.php'; // Optional: redirect after modal close
                return;
            }

            if (Object.keys(cart).length === 0) {
                showModal('Please add items to your cart.');
                return;
            }

            const cartArray = [];
            let total = 0;
            Object.keys(cart).forEach(item => {
                cartArray.push({name: item, qty: cart[item].qty});
                total += cart[item].qty * cart[item].price;
            });

            const delivery = document.getElementById("deliveryCheck").checked ? 1 : 0;
            if (delivery) total += DELIVERY_FEE;

            // Populate hidden form fields
            document.getElementById("cartData").value = JSON.stringify(cartArray);
            document.getElementById("totalData").value = total.toFixed(2);
            document.getElementById("deliveryData").value = delivery;

            // Submit the order form
            document.getElementById("orderForm").submit();
        }

        // --- Event Listeners ---
        document.getElementById("deliveryCheck").addEventListener("change", calculateTotals);

        document.getElementById("productSearch").addEventListener("keyup", (e) => {
            const searchText = e.target.value.toLowerCase();
            document.querySelectorAll(".product-card").forEach(card => {
                const name = card.dataset.name.toLowerCase();
                card.style.display = name.includes(searchText) ? "block" : "none";
            });
        });
        
        // --- Initialization and Success Handling ---
        updateCartUI(); // Initial calculation and UI update

        <?php if ($orderSuccess): ?>
            const popup = document.getElementById("orderPopup");
            popup.classList.add("show");
            setTimeout(() => {
                // Redirect on success (changed from balance.php)
                window.location.href = 'buy-now.php?order=success'; 
            }, 3000); 
        <?php endif; ?>

    </script>
</body>
</html>