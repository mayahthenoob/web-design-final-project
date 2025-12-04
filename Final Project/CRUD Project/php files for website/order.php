<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout
renderModal(); // Ensure modal function is available

// REDIRECTION CHECK: User must be logged in to order (Rubric Requirement)
if (!$authUser) {
    $_SESSION['intended_url'] = 'order.php';
    $_SESSION['redirect_message'] = 'You must be logged in to place an order.';
    header('Location: login.php');
    exit;
}

// Fetch products
try {
    $stmt = $pdo->query('SELECT product_id, name, price, description, type FROM products ORDER BY name ASC');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    error_log("Product fetch failed: " . $e->getMessage());
}

$orderSuccess = false;
$errorMessage = '';
$deliveryFee = 2.00;

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    $cartDataJson = trim($_POST['cart_data'] ?? '');
    $totalAmount = floatval($_POST['total_amount'] ?? 0);
    $deliveryRequired = intval($_POST['delivery_required'] ?? 0);
    $userId = $authUser['user_id'];
    $currentBalance = $authUser['balance'];

    // 1. Validate Input
    if (empty($cartDataJson) || $totalAmount <= 0) {
        $errorMessage = 'Invalid order data submitted.';
    } elseif ($totalAmount > $currentBalance) {
        $errorMessage = 'Insufficient funds. Your current balance is $' . number_format($currentBalance, 2) . '. Please recharge.';
    } else {
        $cartItems = json_decode($cartDataJson, true);
        
        try {
            // Start Transaction
            $pdo->beginTransaction();

            // 2. Insert into ORDERS table
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, status, delivery_required) VALUES (?, ?, ?, ?)');
            $stmt->execute([$userId, $totalAmount, 'Pending', $deliveryRequired]);
            $orderId = $pdo->lastInsertId();

            // 3. Insert into ORDER_ITEMS table
            $orderDescription = "Order #{$orderId}: ";
            foreach ($cartItems as $item) {
                $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price_at_order) VALUES (?, ?, ?, ?)');
                $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price_at_order']]);
                
                // Build description for transaction
                $orderDescription .= "{$item['quantity']}x {$item['name']} @ $" . number_format($item['price_at_order'], 2) . "; ";
            }

            // 4. Update User Balance (Debit)
            $newBalance = $currentBalance - $totalAmount;
            $stmt = $pdo->prepare('UPDATE users SET balance = ? WHERE user_id = ?');
            $stmt->execute([$newBalance, $userId]);
            
            // Update session balance
            $_SESSION['authUser']['balance'] = $newBalance;
            $authUser['balance'] = $newBalance;

            // 5. Record Transaction (Negative amount for order/purchase)
            $transactionDescription = "Purchase for " . ($deliveryRequired ? "Delivery" : "Pickup") . " - " . rtrim($orderDescription, '; ');
            $stmt = $pdo->prepare('INSERT INTO transactions (user_id, order_id, transaction_type, amount, description) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$userId, $orderId, 'Order', -$totalAmount, $transactionDescription]);


            // Commit Transaction
            $pdo->commit();
            $orderSuccess = true;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Order submission failed: " . $e->getMessage());
            $errorMessage = 'An error occurred while processing your order: ' . $e->getMessage();
        }
    }
}

renderHead('Place Order');
renderHeader('order.php', $authUser);
?>

<main class="container" style="padding: 40px 20px;">
    <h1 style="font-size: 3em; color: var(--secondary-color); text-align: center; margin-bottom: 30px;">Place Your Order</h1>

    <?php if ($errorMessage): ?>
        <div class="error" style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 600;">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>

    <!-- User Balance Display (LIVE) -->
    <div style="background: #e0f2fe; padding: 15px; border-radius: 8px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; font-size: 1.2em; font-weight: 700; border: 1px solid #90cdf4;">
        <span>Your Current Balance:</span>
        <span style="color: #0d9488;">$<?= number_format($authUser['balance'] ?? 0, 2) ?> XCD</span>
    </div>

    <div class="order-layout" style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Product Selection Area -->
        <section class="products-list">
            <h2 style="font-size: 2em; color: var(--primary-color); margin-bottom: 20px;">Select Products</h2>
            
            <input type="text" id="productSearch" placeholder="Search products..." style="width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px;">

            <div class="card-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
                <?php foreach ($products as $product): ?>
                    <div class="product-card" 
                         data-id="<?= $product['product_id'] ?>" 
                         data-name="<?= htmlspecialchars($product['name']) ?>" 
                         data-price="<?= $product['price'] ?>"
                         data-type="<?= $product['type'] ?>"
                         style="border: 1px solid #eee; padding: 15px; border-radius: 8px; box-shadow: var(--shadow-md); background: white;">
                        
                        <h4 style="font-size: 1.2em; color: var(--secondary-color); margin-bottom: 5px;"><?= htmlspecialchars($product['name']) ?></h4>
                        <p style="font-size: 1.4em; font-weight: 700; color: var(--primary-color); margin-bottom: 10px;">$<?= number_format($product['price'], 2) ?></p>
                        
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <button class="btn remove-item" data-id="<?= $product['product_id'] ?>" style="padding: 8px 12px; background: #ef4444;">-</button>
                            <span id="qty-<?= $product['product_id'] ?>" style="padding: 8px 0; text-align: center; width: 40px; border: 1px solid #ddd; border-radius: 5px;">0</span>
                            <button class="btn add-item" data-id="<?= $product['product_id'] ?>" style="padding: 8px 12px;">+</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Cart Summary Area -->
        <section class="cart-summary" style="background: var(--background-light); padding: 20px; border-radius: 10px; box-shadow: var(--shadow-md); position: sticky; top: 80px; height: fit-content;">
            <h2 style="font-size: 1.8em; color: var(--secondary-color); margin-bottom: 20px;">Your Cart</h2>

            <div id="cartItems" style="border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 15px; max-height: 400px; overflow-y: auto;">
                <p style="color: #666;" id="emptyCartMsg">Your cart is empty.</p>
            </div>

            <div class="delivery-option" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding: 10px; border-radius: 5px; background: white;">
                <label for="deliveryCheck" style="font-weight: 600;">Require Delivery ($<?= number_format($deliveryFee, 2) ?>)</label>
                <input type="checkbox" id="deliveryCheck" style="width: 20px; height: 20px;">
            </div>

            <div class="totals-row" style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1em;">
                <span>Subtotal:</span>
                <span id="cartSubtotal" style="font-weight: 600;">$0.00</span>
            </div>
            
            <div class="totals-row" style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 1em;">
                <span>Delivery Fee:</span>
                <span id="deliveryFeeDisplay" style="font-weight: 600;">$0.00</span>
            </div>
            
            <div class="totals-row" style="display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px solid var(--primary-color); font-size: 1.4em;">
                <span style="font-weight: 700;">Total:</span>
                <span id="cartTotal" style="font-weight: 700; color: #ef4444;">$0.00</span>
            </div>

            <!-- Hidden Form for Submission -->
            <form id="orderForm" method="POST" action="order.php" style="margin-top: 20px;">
                <input type="hidden" name="cart_data" id="cartData">
                <input type="hidden" name="total_amount" id="totalData">
                <input type="hidden" name="delivery_required" id="deliveryData">
                <button type="button" class="btn" onclick="processOrder()" style="width: 100%; padding: 12px; font-size: 1.1em;">Confirm & Place Order</button>
            </form>
        </section>
    </div>

    <!-- Success Popup (Replaces old alert) -->
    <div id="orderPopup" style="display: <?= $orderSuccess ? 'flex' : 'none' ?>; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 10000; justify-content: center; align-items: center;">
        <div style="background: #22c55e; color: white; padding: 40px; border-radius: 12px; max-width: 400px; text-align: center; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);">
            <i class='bx bxs-check-circle' style="font-size: 3em; margin-bottom: 15px;"></i>
            <h3 style="font-size: 1.8em; margin-bottom: 10px;">Order Placed!</h3>
            <p style="font-size: 1.1em;">Your order has been successfully submitted. You will be redirected to your Balance page shortly.</p>
        </div>
    </div>
</main>

<script>
    const DELIVERY_FEE = <?= $deliveryFee ?>;
    const authUser = <?= json_encode($authUser) ?>;
    let cart = JSON.parse(localStorage.getItem('flavorfulCart') || '{}');

    document.addEventListener('DOMContentLoaded', () => {
        // Attach click listeners to all product buttons
        document.querySelectorAll('.add-item').forEach(button => {
            button.addEventListener('click', () => updateCart(button.dataset.id, 1));
        });
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', () => updateCart(button.dataset.id, -1));
        });
        document.getElementById("deliveryCheck").addEventListener("change", updateCartUI);
        
        updateCartUI(); // Initial calculation and UI update
    });

    function getProductData(id) {
        const productCard = document.querySelector(`.product-card[data-id="${id}"]`);
        if (productCard) {
            return {
                id: id,
                name: productCard.dataset.name,
                price: parseFloat(productCard.dataset.price),
                type: productCard.dataset.type
            };
        }
        return null;
    }

    function updateCart(id, change) {
        const product = getProductData(id);
        if (!product) return;

        if (!cart[id]) {
            cart[id] = { ...product, qty: 0 };
        }

        cart[id].qty += change;

        if (cart[id].qty <= 0) {
            delete cart[id];
        }

        // Save to localStorage and update UI
        localStorage.setItem('flavorfulCart', JSON.stringify(cart));
        updateCartUI();
    }

    function updateCartUI() {
        let subtotal = 0;
        const cartItemsDiv = document.getElementById('cartItems');
        const deliveryChecked = document.getElementById("deliveryCheck").checked;
        
        cartItemsDiv.innerHTML = '';
        
        // Update product quantity displays and calculate subtotal
        document.querySelectorAll('.product-card').forEach(card => {
            const id = card.dataset.id;
            const qty = cart[id] ? cart[id].qty : 0;
            document.getElementById(`qty-${id}`).innerText = qty;

            if (qty > 0) {
                const item = cart[id];
                const itemTotal = item.qty * item.price;
                subtotal += itemTotal;

                const itemHtml = `
                    <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dotted #ccc;">
                        <span>${item.qty} x ${item.name}</span>
                        <span>$${itemTotal.toFixed(2)}</span>
                    </div>
                `;
                cartItemsDiv.innerHTML += itemHtml;
            }
        });
        
        if (Object.keys(cart).length === 0) {
            cartItemsDiv.innerHTML = '<p style="color: #666;">Your cart is empty.</p>';
        }

        const deliveryFee = deliveryChecked ? DELIVERY_FEE : 0.00;
        const total = subtotal + deliveryFee;

        // Update totals display
        document.getElementById("cartSubtotal").innerText = `$${subtotal.toFixed(2)}`;
        document.getElementById("deliveryFeeDisplay").innerText = `$${deliveryFee.toFixed(2)}`;
        document.getElementById("cartTotal").innerText = `$${total.toFixed(2)}`;
        
        // Update total amount color based on balance
        const balance = parseFloat(authUser.balance || 0);
        const totalDisplay = document.getElementById("cartTotal");
        if (total > balance) {
            totalDisplay.style.color = '#ef4444'; // Red for insufficient funds
        } else {
            totalDisplay.style.color = '#0d9488'; // Green for OK
        }
    }

    function processOrder() {
        const deliveryChecked = document.getElementById("deliveryCheck").checked;
        const total = parseFloat(document.getElementById("cartTotal").innerText.replace('$', ''));
        const balance = parseFloat(authUser.balance || 0);

        if (Object.keys(cart).length === 0) {
            showModal('Your cart is empty. Please add products to place an order.', 'Order Error');
            return;
        }

        if (total > balance) {
            showModal(`Insufficient funds! Order Total: $${total.toFixed(2)}, Your Balance: $${balance.toFixed(2)}. Please recharge your account.`, 'Insufficient Funds');
            return;
        }

        // Prepare data for submission
        const cartArray = Object.values(cart).map(item => ({
            product_id: item.id,
            name: item.name,
            quantity: item.qty,
            price_at_order: item.price // Use the fetched price
        }));

        // Populate hidden form fields
        document.getElementById("cartData").value = JSON.stringify(cartArray);
        document.getElementById("totalData").value = total.toFixed(2);
        document.getElementById("deliveryData").value = deliveryChecked ? 1 : 0;

        // Submit the order form
        document.getElementById("orderForm").submit();
    }

    // --- Search functionality ---
    document.getElementById("productSearch").addEventListener("keyup", (e) => {
        const searchText = e.target.value.toLowerCase();
        document.querySelectorAll(".product-card").forEach(card => {
            const name = card.dataset.name.toLowerCase();
            card.style.display = name.includes(searchText) ? "block" : "none";
        });
    });
    
    // --- Success Handling ---
    <?php if ($orderSuccess): ?>
        // Clear cart after successful submission
        cart = {};
        localStorage.removeItem('flavorfulCart');
        
        const popup = document.getElementById("orderPopup");
        popup.style.display = "flex";

        setTimeout(() => {
            // Redirect to balance page after order
            window.location.href = 'balance.php'; 
        }, 3000); 
    <?php endif; ?>

</script>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>