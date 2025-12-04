<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout

// This page is now a static information page as detailed ordering is moved to order.php
$deliveryFee = 2.00;

renderHead('Delivery Service');
renderHeader('delivery.php', $authUser);
?>

<main class="container" style="padding: 40px 20px;">
    <h1 style="font-size: 3em; color: var(--secondary-color); text-align: center; margin-bottom: 30px;">Island-wide Delivery Service</h1>
    
    <section class="delivery-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-bottom: 50px;">
        
        <div class="info-card" style="padding: 25px; border-radius: 10px; box-shadow: var(--shadow-md); background: #f0fdf4; border-left: 5px solid #10b981;">
            <h2 style="font-size: 1.5em; color: #10b981; margin-bottom: 10px;">Fast Turnaround</h2>
            <p style="color: #444;">Orders placed before 12 PM are typically delivered the same day across the island.</p>
        </div>
        
        <div class="info-card" style="padding: 25px; border-radius: 10px; box-shadow: var(--shadow-md); background: #fef2f2; border-left: 5px solid #ef4444;">
            <h2 style="font-size: 1.5em; color: #ef4444; margin-bottom: 10px;">Fixed Delivery Fee</h2>
            <p style="color: #444;">A flat fee of **$<?= number_format($deliveryFee, 2) ?> XCD** applies to all delivery orders, regardless of size.</p>
        </div>
        
        <div class="info-card" style="padding: 25px; border-radius: 10px; box-shadow: var(--shadow-md); background: #eff6ff; border-left: 5px solid #3b82f6;">
            <h2 style="font-size: 1.5em; color: #3b82f6; margin-bottom: 10px;">Coverage Area</h2>
            <p style="color: #444;">We deliver to all major parishes. Please ensure your address is accurate upon ordering.</p>
        </div>
    </section>
    
    <section class="how-to-order" style="text-align: center; margin-bottom: 50px;">
        <h2 style="font-size: 2em; color: var(--secondary-color); margin-bottom: 20px;">How to Arrange Delivery</h2>
        <div class="steps-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div style="padding: 20px; border: 1px dashed #ccc; border-radius: 8px;">
                <i class='bx bxs-cart' style="font-size: 2.5em; color: var(--primary-color); margin-bottom: 10px;"></i>
                <p style="font-weight: 600;">1. Go to the <a href="order.php" style="color: var(--primary-color);">Order Page</a></p>
            </div>
            <div style="padding: 20px; border: 1px dashed #ccc; border-radius: 8px;">
                <i class='bx bxs-shopping-bag' style="font-size: 2.5em; color: var(--primary-color); margin-bottom: 10px;"></i>
                <p style="font-weight: 600;">2. Fill your cart with the desired items.</p>
            </div>
            <div style="padding: 20px; border: 1px dashed #ccc; border-radius: 8px;">
                <i class='bx bxs-truck' style="font-size: 2.5em; color: var(--primary-color); margin-bottom: 10px;"></i>
                <p style="font-weight: 600;">3. Check the "Require Delivery" option at checkout.</p>
            </div>
            <div style="padding: 20px; border: 1px dashed #ccc; border-radius: 8px;">
                <i class='bx bxs-check-shield' style="font-size: 2.5em; color: var(--primary-color); margin-bottom: 10px;"></i>
                <p style="font-weight: 600;">4. Confirm and pay using your account balance.</p>
            </div>
        </div>
    </section>
    
    <div style="text-align: center; margin-top: 40px;">
        <a href="order.php" class="btn" style="padding: 12px 30px; font-size: 1.1em;">Start Your Delivery Order Now</a>
    </div>

</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>