<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout

renderHead('Social Media');
renderHeader('socials.php', $authUser);
?>

<main class="container" style="padding: 40px 20px;">
    <section class="socials-intro" style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 3em; color: var(--secondary-color); margin-bottom: 10px;">Connect With Us</h1>
        <p style="font-size: 1.1em; color: #555;">Follow us on our channels to see the latest flavors, promotions, and community updates!</p>
    </section>

    <section class="recent">
        <div class="card-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">

            <!-- Facebook -->
            <div class="card" style="border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-md); text-align: center; background: #3b5998; color: white; transition: transform 0.3s;">
                <img src="https://placehold.co/400x150/3b5998/ffffff?text=Facebook" alt="Facebook Page" style="width: 100%; height: 150px; object-fit: cover; opacity: 0.8;">
                <div class="card-content" style="padding: 20px;">
                    <i class="fab fa-facebook-f" style="font-size: 2em; margin-bottom: 10px;"></i>
                    <h4 style="font-size: 1.4em; margin-bottom: 5px;">Facebook</h4>
                    <p style="font-size: 0.95em;">Stay updated with our community events and daily flavor announcements.</p>
                    <a href="https://facebook.com/flavorfulgd" target="_blank" style="margin-top: 15px; display: inline-block; color: #fff; font-weight: 600; border-bottom: 1px solid white;">Visit Our Page &rarr;</a>
                </div>
            </div>

            <!-- Instagram -->
            <div class="card" style="border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-md); text-align: center; background: #e1306c; color: white; transition: transform 0.3s;">
                <img src="https://placehold.co/400x150/e1306c/ffffff?text=Instagram" alt="Instagram Page" style="width: 100%; height: 150px; object-fit: cover; opacity: 0.8;">
                <div class="card-content" style="padding: 20px;">
                    <i class="fab fa-instagram" style="font-size: 2em; margin-bottom: 10px;"></i>
                    <h4 style="font-size: 1.4em; margin-bottom: 5px;">Instagram</h4>
                    <p style="font-size: 0.95em;">See vibrant photos of our products and behind-the-scenes content.</p>
                    <a href="https://instagram.com/flavorfulgd" target="_blank" style="margin-top: 15px; display: inline-block; color: #fff; font-weight: 600; border-bottom: 1px solid white;">Follow Us &rarr;</a>
                </div>
            </div>

            <!-- WhatsApp -->
            <div class="card" style="border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-md); text-align: center; background: #25d366; color: white; transition: transform 0.3s;">
                <img src="https://placehold.co/400x150/25d366/ffffff?text=WhatsApp" alt="WhatsApp Chat" style="width: 100%; height: 150px; object-fit: cover; opacity: 0.8;">
                <div class="card-content" style="padding: 20px;">
                    <i class="fab fa-whatsapp" style="font-size: 2em; margin-bottom: 10px;"></i>
                    <h4 style="font-size: 1.4em; margin-bottom: 5px;">WhatsApp</h4>
                    <p style="font-size: 0.95em;">Quickly contact us for support, bulk inquiries, or immediate order questions.</p>
                    <a href="https://wa.me/14734562535" target="_blank" style="margin-top: 15px; display: inline-block; color: #fff; font-weight: 600; border-bottom: 1px solid white;">Message Us Now &rarr;</a>
                </div>
            </div>

        </div>
    </section>

    <section class="email-signup" style="text-align: center; padding: 40px; margin-top: 50px; background: var(--background-light); border-radius: 10px; box-shadow: var(--shadow-md);">
        <h2 style="font-size: 2em; color: var(--secondary-color); margin-bottom: 10px;">Subscribe to our Newsletter</h2>
        <p style="margin-bottom: 20px; color: #555;">Get exclusive deals and first dibs on new flavors delivered right to your inbox.</p>
        <form style="display: flex; justify-content: center; gap: 10px; max-width: 500px; margin: 0 auto;">
            <input type="email" placeholder="Enter your email address" required style="padding: 12px; flex-grow: 1; border: 1px solid #ddd; border-radius: 5px;">
            <button type="submit" class="btn" style="padding: 12px 25px;">Subscribe</button>
        </form>
    </section>
</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>