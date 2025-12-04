<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout

renderHead('Order Options');
renderHeader('buy-now.php', $authUser);

// Display message if redirected from a page requiring login
$message = $_SESSION['redirect_message'] ?? '';
unset($_SESSION['redirect_message']);
?>

<main class="main-content container" style="padding: 60px 20px; text-align: center;">
    <h1 style="font-size: 3em; color: var(--secondary-color); margin-bottom: 10px;">Start Your Flavorful Order</h1>
    <p style="font-size: 1.2em; color: #555; margin-bottom: 40px;">Select an option below to proceed with ordering, calculate your needs, or check delivery details.</p> 

    <?php if ($message): ?>
        <div style="background-color: #fef3c7; color: #d97706; padding: 15px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #fde68a; font-weight: 600;">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
        <!-- New Order Page -->
        <a href="order.php" class="card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; background: var(--primary-color); color: white; border-radius: 12px; box-shadow: var(--shadow-md); transition: transform 0.3s; text-decoration: none;">
            <i class='bx bxs-shopping-bag' style="font-size: 3.5em; margin-bottom: 15px;"></i>
            <span style="font-size: 1.5em; font-weight: 700;">Place Bulk/Pickup Order</span>
            <p style="font-size: 0.9em; margin-top: 5px;">Place an order using your account balance.</p>
        </a>
        
        <!-- Delivery Page -->
        <a href="delivery.php" class="card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; background: #3b82f6; color: white; border-radius: 12px; box-shadow: var(--shadow-md); transition: transform 0.3s; text-decoration: none;">
            <i class='bx bxs-truck' style="font-size: 3.5em; margin-bottom: 15px;"></i>
            <span style="font-size: 1.5em; font-weight: 700;">Arrange Delivery</span>
            <p style="font-size: 0.9em; margin-top: 5px;">See costs and schedule delivery.</p>
        </a>
        
        <!-- Calculator Page -->
        <a href="calculator.php" class="card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; background: #60a5fa; color: white; border-radius: 12px; box-shadow: var(--shadow-md); transition: transform 0.3s; text-decoration: none;">
            <i class='bx bx-calculator' style="font-size: 3.5em; margin-bottom: 15px;"></i>
            <span style="font-size: 1.5em; font-weight: 700;">Use Calculator</span>
            <p style="font-size: 0.9em; margin-top: 5px;">Estimate total costs quickly.</p>
        </a>
        
        <?php if (!$authUser): ?>
            <!-- Login Button -->
            <a href="login.php?intended_url=balance.php" class="card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; background: #6b7280; color: white; border-radius: 12px; box-shadow: var(--shadow-md); transition: transform 0.3s; text-decoration: none;">
              <i class='bx bx-user' style="font-size: 3.5em; margin-bottom: 15px;"></i>
              <span style="font-size: 1.5em; font-weight: 700;">Log In / View Balance</span>
              <p style="font-size: 0.9em; margin-top: 5px;">Access your account and order history.</p>
            </a>
        <?php else: ?>
            <!-- View Balance/Orders Button for logged in users -->
            <a href="balance.php" class="card" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; background: #0d9488; color: white; border-radius: 12px; box-shadow: var(--shadow-md); transition: transform 0.3s; text-decoration: none;">
              <i class='bx bxs-wallet' style="font-size: 3.5em; margin-bottom: 15px;"></i>
              <span style="font-size: 1.5em; font-weight: 700;">View Account Balance</span>
              <p style="font-size: 0.9em; margin-top: 5px;">See your balance, orders, and transactions.</p>
            </a>
        <?php endif; ?>
    </div>
</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>