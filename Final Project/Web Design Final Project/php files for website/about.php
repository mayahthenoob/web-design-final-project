<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout

renderHead('About Us');
renderHeader('about.php', $authUser);
?>

<main class="container" style="padding: 40px 20px;">
    <section class="company-intro" style="text-align: center; margin-bottom: 50px;">
        <h1 style="font-size: 3em; color: var(--secondary-color); margin-bottom: 10px;">Our Journey to Flavorful</h1>
        <p style="font-size: 1.1em; color: #555; max-width: 800px; margin: 0 auto;">
            Founded on the principle of delivering the freshest, most authentic local flavors, Flavorful has grown from 
            a small operation to a trusted name in St. Andrew's community and beyond.
        </p>
    </section>

    <section class="mission-vision" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; margin-bottom: 60px;">
        <div style="background: var(--background-light); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); border-top: 4px solid var(--primary-color);">
            <h2 style="font-size: 2em; color: var(--primary-color); border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">Our Mission</h2>
            <p style="color: #444;">
                To delight our customers with the best-tasting, highest-quality frozen treats in Grenada, while supporting 
                local farmers and adhering to stringent sanitation standards. We aim to be the island's favorite pennacool supplier.
            </p>
        </div>
        <div style="background: var(--background-light); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md); border-top: 4px solid var(--secondary-color);">
            <h2 style="font-size: 2em; color: var(--secondary-color); border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">Our Vision</h2>
            <p style="color: #444;">
                To expand our flavorful reach across the Caribbean, becoming a recognized regional brand synonymous with 
                refreshment, quality, and Caribbean authenticity, all while maintaining a sustainable business model.
            </p>
        </div>
    </section>
    
    <section class="core-values">
        <h2 style="font-size: 2.2em; color: var(--secondary-color); text-align: center; margin-bottom: 30px;">Core Values</h2>
        <div class="values-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <div class="card" style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-md);">
                <img src="https://placehold.co/400x200/f59e0b/ffffff?text=Fresh+Ingredients" alt="Fresh local fruit" style="width: 100%; height: 200px; object-fit: cover;">
                <div class="card-content" style="padding: 20px;">
                    <span style="display: block; font-size: 0.9em; color: #999; margin-bottom: 5px;">Source & Taste</span>
                    <h4 style="font-size: 1.4em; color: var(--secondary-color); margin-bottom: 10px;">Authentic Caribbean Flavors</h4>
                    <p style="font-size: 0.95em; color: #666;">We are committed to using the best ingredients to capture the true taste of the islands.</p>
                </div>
            </div>
            <div class="card" style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-md);">
                <img src="https://placehold.co/400x200/d97706/ffffff?text=Quality+Control" alt="Quality check" style="width: 100%; height: 200px; object-fit: cover;">
                <div class="card-content" style="padding: 20px;">
                    <span style="display: block; font-size: 0.9em; color: #999; margin-bottom: 5px;">Quality Assurance</span>
                    <h4 style="font-size: 1.4em; color: var(--secondary-color); margin-bottom: 10px;">Fresh, local ingredients with strict sanitation protocols</h4>
                    <p style="font-size: 0.95em; color: #666;">We pride ourselves on offering a refreshing and high-quality product to all our customers.</p>
                </div>
            </div>
            <div class="card" style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-md);">
                <img src="https://placehold.co/400x200/3b82f6/ffffff?text=Delivery+Network" alt="Delivery Van" style="width: 100%; height: 200px; object-fit: cover;">
                <div class="card-content" style="padding: 20px;">
                    <span style="display: block; font-size: 0.9em; color: #999; margin-bottom: 5px;">Logistics</span>
                    <h4 style="font-size: 1.4em; color: var(--secondary-color); margin-bottom: 10px;">Fast and Reliable Delivery</h4>
                    <p style="font-size: 0.95em; color: #666;">Utilizing our delivery network, we ensure your order reaches you promptly and in perfect condition.</p>
                    <a href="delivery.php" style="margin-top: 15px; display: inline-block; font-weight: 600; color: #3b82f6; text-decoration: none;">View Delivery Details &rarr;</a>
                </div>
            </div>
        </div>
    </section>

</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>