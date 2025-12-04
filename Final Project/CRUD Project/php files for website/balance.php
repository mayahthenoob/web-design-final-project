<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout
renderModal(); // Ensure modal function is available

// Redirection check: User must be logged in to view their balance
if (!$authUser) {
    $_SESSION['redirect_message'] = 'You must be logged in to view your balance and order history.';
    // Set intended URL to ensure redirect back after login
    $_SESSION['intended_url'] = 'balance.php'; 
    header('Location: login.php');
    exit;
}

$currentUserId = $authUser['user_id'];
// Fetch latest balance from DB to ensure it's up-to-date
try {
    $stmt = $pdo->prepare('SELECT balance FROM users WHERE user_id = ?');
    $stmt->execute([$currentUserId]);
    $dbBalance = $stmt->fetchColumn();
    $balance = (float)$dbBalance;
    // Update session with latest balance
    $_SESSION['authUser']['balance'] = $balance;
    $authUser['balance'] = $balance;

} catch (PDOException $e) {
    error_log("Balance fetch failed: " . $e->getMessage());
    $balance = $authUser['balance']; // Fallback to session value
}

// Check for redirect messages (e.g., from successful login/register/order)
$redirectMessage = $_SESSION['redirect_message'] ?? '';
unset($_SESSION['redirect_message']);


// Fetch Transaction History
$transactions = [];
try {
    $stmt = $pdo->prepare('SELECT transaction_id, order_id, transaction_type, amount, description, transaction_date FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC');
    $stmt->execute([$currentUserId]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Transaction history fetch failed: " . $e->getMessage());
}

// Fetch Order History (just for item details, transactions cover the financial history)
$orders = [];
$orderIds = array_column($transactions, 'order_id');
$orderIds = array_filter(array_unique($orderIds)); // Get unique, non-null order IDs

if (!empty($orderIds)) {
    try {
        // Use IN clause to fetch all relevant orders in one go
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $orderStmt = $pdo->prepare("SELECT order_id, delivery_required FROM orders WHERE order_id IN ($placeholders)");
        $orderStmt->execute($orderIds);
        $orders = $orderStmt->fetchAll(PDO::FETCH_KEY_PAIR); // order_id => delivery_required
        
        // Fetch all order items efficiently
        $itemStmt = $pdo->prepare('
            SELECT oi.order_id, oi.quantity, oi.price_at_order, p.name 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id IN (' . $placeholders . ')
        ');
        $itemStmt->execute($orderIds);
        $orderItems = $itemStmt->fetchAll();

        // Structure items under their respective orders
        $itemsByOrder = [];
        foreach ($orderItems as $item) {
            $itemsByOrder[$item['order_id']][] = $item;
        }

        // Merge order details into transactions (optional, but helpful for display)
        foreach ($transactions as &$transaction) {
            if ($transaction['order_id'] && isset($orders[$transaction['order_id']])) {
                $transaction['delivery_required'] = $orders[$transaction['order_id']];
                $transaction['items'] = $itemsByOrder[$transaction['order_id']] ?? [];
            } else {
                $transaction['delivery_required'] = 0;
                $transaction['items'] = [];
            }
        }
        unset($transaction); // Clear reference
        
    } catch (PDOException $e) {
        error_log("Order item fetch failed: " . $e->getMessage());
    }
}


renderHead('Account Balance & Transactions');
renderHeader('balance.php', $authUser);
?>

<main class="container" style="padding: 40px 20px;">
    <h1 style="font-size: 3em; color: var(--secondary-color); text-align: center; margin-bottom: 30px;">Account Overview</h1>

    <?php if ($redirectMessage): ?>
        <div style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 600;">
            <?php echo htmlspecialchars($redirectMessage); ?>
        </div>
    <?php endif; ?>

    <!-- Balance Card -->
    <section class="balance-card" style="background: var(--primary-color); color: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); text-align: center; margin-bottom: 40px;">
        <p style="font-size: 1.2em; margin-bottom: 5px;">Current Available Balance</p>
        <h2 style="font-size: 4em; font-weight: 700;">$<?= number_format($balance, 2) ?> <small style="font-size: 0.4em; opacity: 0.8;">XCD</small></h2>
        <p style="font-size: 0.9em; margin-top: 10px;">User: <?= htmlspecialchars($authUser['username'] ?? 'N/A') ?> | ID: <?= htmlspecialchars($authUser['user_id'] ?? 'N/A') ?></p>
        <a href="contact.php?recharge=true" class="btn" style="background: white; color: var(--primary-color); margin-top: 20px;">Recharge Account</a>
    </section>

    <!-- Transaction History -->
    <section class="transaction-history">
        <h2 style="font-size: 2.2em; color: var(--secondary-color); border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">Transaction History</h2>

        <?php if (empty($transactions)): ?>
            <p style="text-align: center; color: #666; font-size: 1.1em;">No transactions recorded yet. <a href="buy-now.php" style="color: var(--primary-color); font-weight: 600;">Start your first order!</a></p>
        <?php else: ?>
            <div class="transaction-list" style="display: grid; gap: 15px;">
                <?php foreach ($transactions as $transaction): 
                    $isOrder = $transaction['transaction_type'] === 'Order';
                    $amountClass = $transaction['amount'] < 0 ? 'text-red' : 'text-green';
                    $amountColor = $transaction['amount'] < 0 ? '#ef4444' : '#22c55e';
                    $date = date('M d, Y h:i A', strtotime($transaction['transaction_date']));
                ?>
                    <div class="transaction-item" style="display: grid; grid-template-columns: 1fr 2fr 1fr; align-items: center; padding: 15px; background: white; border-radius: 8px; box-shadow: var(--shadow-md); border-left: 5px solid <?= $amountColor ?>;">
                        
                        <div class="type-date">
                            <span style="font-weight: 700; color: var(--secondary-color); font-size: 1.1em;"><?= htmlspecialchars($transaction['transaction_type']) ?></span>
                            <p style="font-size: 0.9em; color: #666;"><?= $date ?></p>
                        </div>

                        <div class="description" style="color: #444; font-size: 0.95em;">
                            <?= htmlspecialchars($transaction['description']) ?>
                        </div>

                        <div class="amount" style="text-align: right;">
                            <span style="font-size: 1.4em; font-weight: 700; color: <?= $amountColor ?>;">
                                <?= number_format($transaction['amount'], 2) ?>
                            </span>
                        </div>
                        
                        <?php if ($isOrder && !empty($transaction['items'])): ?>
                            <div style="grid-column: 1 / -1; margin-top: 10px;">
                                <details>
                                    <summary style="cursor: pointer; font-weight: 600; color: var(--primary-color);">
                                        View Order Details (<?= count($transaction['items']); ?> Items)
                                    </summary>
                                    <ul style="list-style: none; padding-left: 0; margin-top: 10px; background: #fafafa; padding: 10px; border-radius: 5px;">
                                        <?php foreach ($transaction['items'] as $item): ?>
                                            <li style="padding: 5px 0; border-top: 1px dotted #f0f0f0; display: flex; justify-content: space-between; font-size: 0.9em;">
                                                <span><?= htmlspecialchars($item['quantity']); ?> x <?= htmlspecialchars($item['name']); ?></span>
                                                <span>@ $<?= number_format($item['price_at_order'], 2); ?> each</span>
                                            </li>
                                        <?php endforeach; ?>
                                        <li style="padding-top: 8px; font-weight: 700; border-top: 2px solid #ccc; display: flex; justify-content: space-between;">
                                            <span>Total (including delivery):</span>
                                            <span>$<?= number_format(-$transaction['amount'], 2); ?></span>
                                        </li>
                                    </ul>
                                </details>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</main>

<?php if (isset($_SESSION['recharge_success'])): ?>
<script>
    // Use the custom modal instead of alert()
    showModal("Recharge Successful! Your balance is now $<?= number_format($balance, 2); ?>", "Balance Update");
    <?php unset($_SESSION['recharge_success']); ?> 
</script>
<?php endif; ?>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>