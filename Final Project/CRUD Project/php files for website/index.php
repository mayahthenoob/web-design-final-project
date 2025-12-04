<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout

renderHead('Homepage');
renderHeader('index.php', $authUser);
?>

<main>
    <section class="hero-section container" style="padding: 60px 20px; display: flex; flex-direction: column; align-items: center; text-align: center;">
        <div class="hero-text" style="max-width: 800px; margin-bottom: 30px;">
            <h2 style="font-size: 3.5em; color: var(--secondary-color); margin-bottom: 15px;">Flavorful: The Best Pennacool in Grenada</h2>
            <p style="font-size: 1.2em; color: #555; line-height: 1.7;">
                Flavorful is dedicated to providing high-quality, refreshing pennacool with exceptional customer service. 
                We offer a variety of flavors and flexible order options to satisfy both individual customers and large retailers.
            </p>
            <a href="buy-now.php" class="btn" style="margin-top: 25px; padding: 12px 30px; font-size: 1.1em;">Place an Order Now</a>
        </div>
        <img src="https://placehold.co/800x400/f59e0b/ffffff?text=Flavorful+Product+Showcase" alt="A collection of Flavorful pennacool products" 
             style="width: 100%; max-width: 800px; height: auto; border-radius: 10px; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);">
    </section>

    <section class="features-section container" style="padding: 40px 20px; text-align: center;">
        <h3 style="font-size: 2.2em; color: var(--secondary-color); margin-bottom: 30px;">Why Choose Flavorful?</h3>
        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="padding: 20px; border-radius: 10px; background: var(--background-light); box-shadow: var(--shadow-md);">
                <i class="bx bxs-leaf" style="font-size: 3em; color: green; margin-bottom: 10px;"></i>
                <h4 style="font-size: 1.4em; margin-bottom: 5px;">Local & Fresh</h4>
                <p>We use locally-sourced, fresh ingredients for the most authentic taste.</p>
            </div>
            <div style="padding: 20px; border-radius: 10px; background: var(--background-light); box-shadow: var(--shadow-md);">
                <i class="bx bxs-badge-check" style="font-size: 3em; color: var(--primary-color); margin-bottom: 10px;"></i>
                <h4 style="font-size: 1.4em; margin-bottom: 5px;">Quality Guaranteed</h4>
                <p>Strict sanitation and quality control protocols are applied to every batch.</p>
            </div>
            <div style="padding: 20px; border-radius: 10px; background: var(--background-light); box-shadow: var(--shadow-md);">
                <i class="bx bxs-truck" style="font-size: 3em; color: #3b82f6; margin-bottom: 10px;"></i>
                <h4 style="font-size: 1.4em; margin-bottom: 5px;">Fast Delivery</h4>
                <p>Reliable and prompt delivery service across the island. See our <a href="delivery.php" style="color: #3b82f6;">Delivery Info</a>.</p>
            </div>
        </div>
    </section>
</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>