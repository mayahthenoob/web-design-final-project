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

// User auth
$authUser = $_SESSION['authUser'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: delivery.php');
    exit;
}

// Fetch products
try {
    // Only fetch 'single' items for delivery (or all if that's the intention)
    $stmt = $pdo->query('SELECT name, price FROM products WHERE type = "single" ORDER BY name ASC');
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
    error_log("Product fetch failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Flavorful - Delivery</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
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

    /* Main Layout */
    .delivery-container {
        display: flex;
        padding: 40px 60px;
        max-width: 1400px;
        margin: 0 auto;
        gap: 30px;
        flex-grow: 1;
    }

    .product-list, .order-summary {
        padding: 20px;
        border-radius: 8px;
        background: #f9f9f9;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    
    .product-list {
        flex: 2;
    }
    .order-summary {
        flex: 1;
        min-width: 300px;
        position: sticky;
        top: 100px;
        align-self: flex-start;
    }

    .product-list h2, .order-summary h2 {
        color: #d97706;
        margin-bottom: 20px;
        font-size: 1.8em;
    }

    /* Search Bar */
    .search-box {
        margin-bottom: 20px;
        position: relative;
    }
    #deliverySearch {
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

    /* Product Grid */
    .delivery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
    }
    .delivery-card {
        background: white;
        border: 1px solid #eee;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .card-placeholder {
        width: 100px;
        height: 100px;
        background: #fef3c7;
        border-radius: 50%;
        margin: 0 auto 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.8em;
        color: #d97706;
        font-weight: 600;
    }
    .delivery-card h4 {
        margin-bottom: 5px;
        font-size: 1.1em;
    }
    .delivery-card p {
        color: #666;
        margin-bottom: 10px;
    }
    .delivery-card button {
        background-color: #f59e0b;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        font-weight: 600;
    }
    .delivery-card button:hover {
        background-color: #d97706;
    }

    /* Cart Summary */
    #deliveryCartItems {
        border-top: 1px solid #eee;
        padding-top: 20px;
        margin-top: 20px;
        min-height: 100px;
    }
    .delivery-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        gap: 15px;
        padding: 10px 0;
        border-bottom: 1px dotted #ccc;
    }
    .item-image-placeholder {
        width: 40px;
        height: 40px;
        background: #f59e0b;
        color: white;
        border-radius: 4px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 0.7em;
        overflow: hidden;
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

    /* RESPONSIVENESS */
    @media (max-width: 1024px) {
        .delivery-container {
            flex-direction: column;
            padding: 40px 20px;
        }
        .order-summary {
            position: static;
            width: 100%;
            margin-top: 30px;
        }
        .delivery-grid {
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        }
    }
    @media (max-width: 768px) {
        header {
            padding: 15px 20px;
            flex-direction: column;
            align-items: flex-start;
        }
        nav { width: 100%; justify-content: space-between; margin-top: 10px; }
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
                <a href="buy-now.php" class="profile-btn" title="View Order Options"> 
                    <span><?= htmlspecialchars(substr($authUser['username'] ?? 'US', 0, 2)); ?></span>
                </a>
                <form method="POST" style="display:inline;">
                    <button type="submit" name="logout">Logout</button>
                </form>
            </div>
        </nav>
    </header>

    <main class="delivery-container">
        <!-- Product List Section -->
        <section class="product-list">
            <h2>Select Pennacools for Delivery</h2>
            
            <div class="search-box">
                <i class='bx bx-search'></i>
                <input type="text" id="deliverySearch" placeholder="Search for a flavor...">
            </div>

            <div class="delivery-grid" id="deliveryProductGrid">
                <!-- Products will be loaded here by JavaScript -->
            </div>
        </section>

        <!-- Order Summary Section -->
        <section class="order-summary">
            <h2>Your Delivery Cart</h2>
            <div id="deliveryCartItems">
                <p style="color:#666; font-style:italic;">Your cart is empty.</p>
            </div>
            
            <div class="totals-box">
                <div><span>Subtotal:</span> <span id="deliverySubtotal">$0.00</span></div>
                <div><span>Delivery Fee:</span> <span>$2.00</span></div>
                <div class="total"><span>Total:</span> <span id="deliveryTotal">$2.00</span></div>
            </div>

            <button class="confirm-btn" onclick="confirmDeliveryOrder()">Place Delivery Order</button>
            <p style="font-size: 0.8em; text-align: center; margin-top: 10px; color: #666;">Delivery fee is fixed at $2.00.</p>
        </section>
    </main>

    <footer>
        &copy; 2025 Flavorful. | All rights reserved.
    </footer>

    <!-- Message Modal (for replacing alert()) -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <p id="modalMessage"></p>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>

    <script>
        const products = <?php echo json_encode($products); ?>;
        let deliveryCart = {};
        const authUser = <?php echo json_encode($authUser); ?>;

        function showModal(message) {
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('messageModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
        }

        function loadDeliveryProducts() {
            let productGrid = document.getElementById("deliveryProductGrid");
            productGrid.innerHTML = '';
            products.forEach(product => {
                productGrid.innerHTML += `
                <div class="delivery-card" data-name="${product.name.toLowerCase()}">
                    <div class="card-placeholder">${product.name.substring(0, 3)}</div>
                    <h4>${product.name}</h4>
                    <p>$${parseFloat(product.price).toFixed(2)}</p>
                    <button onclick="addDeliveryItem('${product.name}', ${product.price})">Add to Cart</button>
                </div>`;
            });
        }
        loadDeliveryProducts();

        function addDeliveryItem(name, price){
            if(!deliveryCart[name]) deliveryCart[name] = {qty:0, price: parseFloat(price)};
            deliveryCart[name].qty++;
            updateDeliveryCartUI();
        }

        function updateDeliveryCartUI(){
            let cartBox = document.getElementById("deliveryCartItems");
            cartBox.innerHTML = "";
            let subtotal = 0;

            if (Object.keys(deliveryCart).length === 0) {
                cartBox.innerHTML = '<p style="color:#666; font-style:italic;">Your cart is empty.</p>';
            } else {
                Object.keys(deliveryCart).forEach(item => {
                    let qty = deliveryCart[item].qty;
                    let price = deliveryCart[item].price;
                    let line = qty * price;
                    subtotal += line;

                    cartBox.innerHTML += `
                    <div class="delivery-item">
                        <div class="item-image-placeholder">${item.substring(0, 3)}</div>
                        <div>
                            <h4>${item} (x${qty})</h4>
                            <p style="font-size:12px;color:#666;">$${price.toFixed(2)} ea</p>
                            <p style="font-weight:600; color:#d97706;">$${line.toFixed(2)}</p>
                        </div>
                    </div>`;
                });
            }

            const total = subtotal + 2.00; // Add fixed $2.00 delivery fee
            document.getElementById("deliverySubtotal").innerText = `$${subtotal.toFixed(2)}`;
            document.getElementById("deliveryTotal").innerText = `$${total.toFixed(2)}`;
        }

        document.getElementById("deliverySearch").addEventListener("keyup",()=>{
            let term = document.getElementById("deliverySearch").value.toLowerCase();
            document.querySelectorAll(".delivery-card").forEach(card=>{
                card.style.display = card.dataset.name.includes(term) ? "block" : "none";
            });
        });

        // Placeholder function for order confirmation (since this is client-side)
        function confirmDeliveryOrder() {
            if (!authUser) {
                showModal('Please login to place an order.');
                // Optionally redirect: window.location.href = 'login.php';
                return;
            }

            if (Object.keys(deliveryCart).length === 0) {
                showModal('Please add items to your cart before placing an order.');
                return;
            }

            // In a real application, you would now submit the cart data to a PHP script via fetch or a hidden form
            let orderSummary = "Thank you for your order, " + authUser.username + "!\n\n";
            let subtotal = 0;
            Object.keys(deliveryCart).forEach(item => {
                let qty = deliveryCart[item].qty;
                let price = deliveryCart[item].price;
                let line = qty * price;
                subtotal += line;
                orderSummary += `${item}: ${qty} x $${price.toFixed(2)} = $${line.toFixed(2)}\n`;
            });
            orderSummary += `\nSubtotal: $${subtotal.toFixed(2)}\nDelivery Fee: $2.00\nTotal: $${(subtotal + 2.00).toFixed(2)}`;
            
            showModal(orderSummary + "\n\n(This is a demonstration. You would be redirected to a payment/confirmation page in a live site.)");
        }
    </script>
</body>
</html>