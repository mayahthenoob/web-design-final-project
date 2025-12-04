<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout
renderModal(); // Ensure modal function is available

$status = null;
$msg = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fullname'])) {
    $name = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Simple validation
    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = 'error';
        $msg = 'Please ensure all fields are correctly filled.';
    } else {
        // In a real application, you would send an email here.
        // For demonstration, we simulate success and log the data.
        error_log("CONTACT FORM SUBMISSION: From $name ($email). Message: $message");
        $status = 'success';
        $msg = 'Thank you for your message! We will get back to you shortly.';

        // Clear POST to prevent resubmission
        $_POST = [];
    }
}

renderHead('Contact Us');
renderHeader('contact.php', $authUser);
?>

<main class="container" style="padding: 40px 20px;">
    <section class="contact-section" style="display: grid; grid-template-columns: 1fr; gap: 40px;">
        <h1 style="grid-column: 1 / -1; text-align: center; font-size: 3em; color: var(--secondary-color); margin-bottom: 15px;">Get In Touch</h1>
        
        <?php if ($status === 'success'): ?>
            <div style="grid-column: 1 / -1; background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 600;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php elseif ($status === 'error'): ?>
            <div style="grid-column: 1 / -1; background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: 600;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <!-- Contact Information -->
        <div class="info-details" style="display: flex; flex-direction: column; gap: 20px; background: var(--background-light); padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
            <h2 style="font-size: 1.8em; color: var(--primary-color); border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 10px;">Our Details</h2>
            
            <div style="display: flex; align-items: center; gap: 15px;">
                <i class='bx bxs-map' style="font-size: 1.5em; color: var(--secondary-color);"></i>
                <div>
                    <h4 style="font-weight: 600;">Address</h4>
                    <p>Flavorful HQ, St. George's, Grenada</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 15px;">
                <i class='bx bxs-phone' style="font-size: 1.5em; color: var(--secondary-color);"></i>
                <div>
                    <h4 style="font-weight: 600;">Phone</h4>
                    <p>+1 (473) 456-2535</p>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 15px;">
                <i class='bx bxs-envelope' style="font-size: 1.5em; color: var(--secondary-color);"></i>
                <div>
                    <h4 style="font-weight: 600;">Email</h4>
                    <p>support@flavorful.gd</p>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form" style="background: white; padding: 30px; border-radius: 10px; box-shadow: var(--shadow-md);">
            <h2 style="font-size: 1.8em; color: var(--secondary-color); margin-bottom: 20px;">Send us a Message</h2>
            <form action="contact.php" method="POST" style="display: grid; gap: 15px;">
                <input type="text" name="fullname" placeholder="Name or Username" required style="padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <input type="email" name="email" placeholder="Email Address" required style="padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <textarea name="message" placeholder="Type your Message..." rows="6" required style="padding: 12px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;"></textarea>
                <button type="submit" class="btn">Send Message</button>
            </form>
        </div>

        <!-- Add Recharge Link for Balance Feature -->
        <div style="grid-column: 1 / -1; text-align: center; margin-top: 20px;">
            <p style="font-size: 1.1em; color: #555;">
                Need to add funds to your account? 
                <a href="contact.php?recharge=true" style="font-weight: 700; color: var(--primary-color);">Contact us about account recharge methods.</a>
            </p>
        </div>
    </section>

    <!-- Responsive Layout using Media Query (Inlined for single file per page) -->
    <style>
        @media (min-width: 768px) {
            .contact-section {
                grid-template-columns: 1fr 1fr;
            }
            .info-details {
                order: 2;
            }
            .contact-form {
                order: 1;
            }
        }
    </style>
</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>