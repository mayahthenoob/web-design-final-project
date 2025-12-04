<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout

renderHead('Our Team');
renderHeader('workers.php', $authUser);
?>

<main class="container" style="padding: 40px 20px;">
    <section class="team-intro" style="text-align: center; margin-bottom: 50px;">
        <h1 style="font-size: 3em; color: var(--secondary-color); margin-bottom: 10px;">Meet the Flavorful Family</h1>
        <p style="font-size: 1.1em; color: #555; max-width: 800px; margin: 0 auto;">
            Our dedicated team works hard every day to bring you the best pennacools on the island. Get to know the faces behind the flavor!
        </p>
    </section>

    <section class="team-members">
        <h2 style="font-size: 2em; color: var(--primary-color); text-align: center; margin-bottom: 30px;">Key Personnel</h2>
        <div class="card-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">

            <!-- Noel -->
            <div class="card" style="border: 1px solid #eee; padding: 25px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center; background: white;">
                <img src="https://placehold.co/150x150/f59e0b/ffffff?text=Noel" alt="Noel" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; margin: 0 auto 15px auto; border: 3px solid var(--primary-color);">
                <h4 style="font-size: 1.5em; color: var(--secondary-color); margin-bottom: 5px;">Noel</h4>
                <span style="display: block; font-size: 1em; color: #666; font-weight: 500; margin-bottom: 15px;">Founder & Manager</span>
                <p style="font-size: 0.9em; color: #444;">The visionary behind Flavorful, Noel overseas all operations of flavorful.</p>
                <div class="socials" style="margin-top: 15px;">
                    <a href="https://wa.me/14734562535" target="_blank" style="color: #666; margin: 0 5px; font-size: 1.5em;"><i class="bx bxl-whatsapp"></i></a>
                    <a href="#" target="_blank" style="color: #666; margin: 0 5px; font-size: 1.5em;"><i class="bx bxl-facebook"></i></a>
                    <a href="#" target="_blank" style="color: #666; margin: 0 5px; font-size: 1.5em;"><i class="bx bxl-instagram"></i></a>
                </div>
            </div>

            <!-- Ezekiel -->
            <div class="card" style="border: 1px solid #eee; padding: 25px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center; background: white;">
                <img src="https://placehold.co/150x150/d97706/ffffff?text=Ezekiel" alt="Ezekiel" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; margin: 0 auto 15px auto; border: 3px solid var(--primary-color);">
                <h4 style="font-size: 1.5em; color: var(--secondary-color); margin-bottom: 5px;">Ezekiel</h4>
                <span style="display: block; font-size: 1em; color: #666; font-weight: 500; margin-bottom: 15px;">Logistics & Operations</span>
                <p style="font-size: 0.9em; color: #444;">Ezekiel handles all delivery and supply chain management, ensuring your order arrives on time.</p>
                <div class="socials" style="margin-top: 15px;">
                    <a href="#" target="_blank" style="color: #666; margin: 0 5px; font-size: 1.5em;"><i class="bx bxl-whatsapp"></i></a>
                    <a href="#" target="_blank" style="color: #666; margin: 0 5px; font-size: 1.5em;"><i class="bx bxl-facebook"></i></a>
                    <a href="#" target="_blank" style="color: #666; margin: 0 5px; font-size: 1.5em;"><i class="bx bxl-instagram"></i></a>
                </div>
            </div>
            
            <!-- (Add more team members here if needed) -->
            <div class="card" style="border: 1px solid #eee; padding: 25px; border-radius: 10px; box-shadow: var(--shadow-md); text-align: center; background: white;">
                <img src="https://placehold.co/150x150/60a5fa/ffffff?text=Local" alt="Local Team Member" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; margin: 0 auto 15px auto; border: 3px solid var(--primary-color);">
                <h4 style="font-size: 1.5em; color: var(--secondary-color); margin-bottom: 5px;">Tom</h4>
                <span style="display: block; font-size: 1em; color: #666; font-weight: 500; margin-bottom: 15px;">Production & Customer Service</span>
                <p style="font-size: 0.9em; color: #444;">Our dedicated employee works daily to maintain the quality and freshness you expect.</p>
                <div class="socials" style="margin-top: 15px;">
                    <a href="#" target="_blank" style="color: #666; margin: 0 5px; font-size: 1.5em;"><i class="bx bxl-whatsapp"></i></a>
                    <a href="#" target="_blank" style="color: #666; margin: 0 5px; font-size: 1.5em;"><i class="bx bxl-facebook"></i></a>
                    <a href="#" target="_blank" style="color: #666; margin: 0 5px; font-size: 1.5em;"><i class="bx bxl-instagram"></i></a>
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