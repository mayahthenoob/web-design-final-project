<?php
session_start();

// Standardized Database Credentials for InfinityFree
$host = "sql300.infinityfree.com";
$db   = "if0_40502206_flavorful"; // Assuming this is the full DB name
$user = "if0_40502206"; // Standard InfinityFree user format
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

// User authentication
$authUser = $_SESSION['authUser'] ?? null;

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: contact.php');
    exit;
}

// Optional: handle contact form submission
$status = null;
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fullname'])) {
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = 'error';
        $msg = 'Please fill out all fields correctly.';
    } else {
        // Insert message into a database table called "messages"
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message, created_at) VALUES (:name, :email, :message, NOW())");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'message' => $message
            ]);
            $status = 'success';
            $msg = 'Message sent successfully! We will get back to you soon.';
        } catch (PDOException $e) {
            error_log("Message insertion failed: " . $e->getMessage());
            $status = 'error';
            $msg = 'There was an error sending your message. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flavorful - Contact Us</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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

        /* Contact Section */
        .contact-section {
            padding: 60px;
            flex-grow: 1;
            background: #f7f7f7;
        }
        .contact-content {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        /* Contact Info */
        .contact-info {
            flex: 1;
            background: #f59e0b;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .contact-info h1 {
            font-size: 2em;
            margin-bottom: 30px;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        .info-icon {
            font-size: 1.5em;
            margin-right: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .info-text h3 {
            margin-bottom: 5px;
            font-size: 1.1em;
        }

        /* Contact Form */
        .contact-form {
            flex: 1;
            padding: 40px;
        }
        .contact-form h2 {
            font-size: 1.8em;
            margin-bottom: 25px;
            color: #111;
        }
        .contact-form input[type="text"],
        .contact-form input[type="email"],
        .contact-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
        }
        .contact-form textarea {
            resize: vertical;
        }
        .contact-form button {
            background-color: #f59e0b;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .contact-form button:hover {
            background-color: #d97706;
        }

        /* Footer Styles */
        footer {
            text-align: center;
            padding: 20px;
            background: #111;
            color: #eee;
            margin-top: auto;
        }

        /* RESPONSIVENESS */
        @media (max-width: 900px) {
            .contact-content {
                flex-direction: column;
            }
            .contact-info {
                padding: 30px;
                text-align: center;
            }
            .contact-info h1 {
                margin-bottom: 20px;
            }
            .info-item {
                justify-content: center;
            }
            .info-icon {
                margin-right: 10px;
            }
            .contact-form {
                padding: 30px;
            }
        }
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-direction: column;
                align-items: flex-start;
            }
            nav { width: 100%; justify-content: space-between; margin-top: 10px; }
            .contact-section {
                padding: 40px 20px;
            }
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
            <a href="contact.php" style="background-color: #f0f0f0;">Contact</a>

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

    <section class="contact-section">
        <div class="contact-content">
            <div class="contact-info">
                <h1>Get In Touch</h1>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="info-text">
                        <h3>Address</h3>
                        <p>Blaize, St. Andrew, Grenada</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-phone"></i></div>
                    <div class="info-text">
                        <h3>Phone</h3>
                        <p>+1 (473) 456-2535</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                    <div class="info-text">
                        <h3>Email</h3>
                        <p>support@flavorful.com</p>
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <?php if(isset($status)): ?>
                    <p style="color: <?php echo $status === 'success' ? 'green' : 'red'; ?>; font-weight: 600; margin-bottom: 15px;"><?php echo htmlspecialchars($msg); ?></p>
                <?php endif; ?>
                <form method="POST">
                    <h2>Send Us a Message</h2>
                    <input type="text" name="fullname" placeholder="Name or Username" required>
                    <input type="email" name="email" placeholder="Email Address" required>
                    <textarea name="message" placeholder="Type your Message..." rows="6" required></textarea>
                    <button type="submit">Send Message</button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        &copy; 2025 Flavorful. | All rights reserved.
    </footer>
</body>
</html>