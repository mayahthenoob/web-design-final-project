<?php
// utils.php - Centralized functions for consistency, security, and responsiveness.

// 1. START SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. DATABASE CONNECTION (PDO for security/prepared statements)
// Standardized Database Credentials (Using user's provided credentials)
$host = "sql300.infinityfree.com";
$db   = "if0_40502206_flavorful";
$user = "if0_40502206";
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
    // In a production environment, you would log this, not die with the error message.
    die("Database connection failed. Please check your configuration and ensure the database is running.");
}


// 3. AUTHENTICATION & LOGOUT HANDLER
$authUser = $_SESSION['authUser'] ?? null;

function handleLogout($redirectPage = 'index.php') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
        // Clear session data
        $_SESSION = [];
        // Destroy session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        // Redirect
        header('Location: ' . $redirectPage);
        exit;
    }
}
handleLogout(); // Run logout check on every page load

// 4. COMMON FUNCTIONS (Head, Header, Footer)

function renderHead($title) {
    echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Flavorful | " . htmlspecialchars($title) . "</title>
    <!-- Boxicons for icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Font Awesome for social media icons (used in socials.php) -->
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css\">
    <style>
        :root {
            --primary-color: #fcd94f;
            --secondary-color: #2c3e50; 
            --background-light: #f9fafb;
            --text-dark: #374151;
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { color: var(--text-dark); background-color: white; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        header { background: white; border-bottom: 1px solid #eee; box-shadow: var(--shadow-md); position: sticky; top: 0; z-index: 1000; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; }
        .logo { font-size: 1.8em; font-weight: 700; color: var(--primary-color); text-decoration: none; }
        .logo:hover { color: #f59e0b;}
        .nav-menu { display: flex; gap: 25px; transition: all 0.3s ease-in-out; }
        .nav-menu a { color: var(--secondary-color); text-decoration: none; font-weight: 500; padding: 5px 0; border-bottom: 2px solid transparent; transition: border-bottom 0.3s; }
        .nav-menu a:hover, .nav-menu a.active { border-bottom: 2px solid var(--primary-color); }
        .auth-links { display: flex; align-items: center; gap: 15px; }
        .login-link, .signup-link { text-decoration: none; padding: 8px 15px; border-radius: 5px; font-weight: 600; }
        .login-link { background: var(--secondary-color); color: white; }
        .signup-link { border: 1px solid var(--primary-color); color: var(--primary-color); }
        .btn { background: var(--primary-color); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: 600; transition: background 0.3s; display: inline-block; }
        .btn:hover { background: #d97706; }
        footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; margin-top: 40px; color: #666; font-size: 0.9em; }
        .profile-icon { display: flex; align-items: center; gap: 10px; }
        .profile-btn { 
            background: var(--primary-color); 
            color: white; 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            font-weight: 700;
            text-decoration: none;
        }
        .profile-icon button { 
            background: none; 
            border: 1px solid #ddd; 
            color: var(--text-dark); 
            padding: 6px 10px; 
            border-radius: 5px; 
            cursor: pointer;
            font-size: 0.9em;
        }
        
        /* Mobile Menu Toggle */
        .menu-toggle { display: none; font-size: 2em; cursor: pointer; color: var(--secondary-color); }

        @media (max-width: 900px) {
            .menu-toggle { display: block; }
            .nav-menu {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background: white;
                box-shadow: var(--shadow-md);
                padding: 10px 20px;
                z-index: 999;
            }
            .nav-menu.active { display: flex; }
            .nav-menu a { padding: 10px 0; border-bottom: 1px solid #eee; }
            .navbar { padding: 10px 0; }
        }
    </style>
    <!-- Use Inter font from Google Fonts -->
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap' rel='stylesheet'>
</head>
<body>\n";
}

function renderHeader($currentPage, $authUser) {
    // Generate user initials for profile button
    $initials = 'G'; // Default
    if ($authUser) {
        $username = $authUser['username'] ?? 'Guest';
        $initials = strtoupper(substr($username, 0, 1));
    }
    
    // Pages to include in the main navbar
    $navPages = [
        'Home' => 'index.php',
        'About' => 'about.php',
        'Prices' => 'prices.php',
        'Team' => 'workers.php',
        'Socials' => 'socials.php',
        'Contact' => 'contact.php', // Corrected filename
        'Buy Now' => 'buy-now.php', // Main action page
    ];
    
    echo '<header>
  <div class="navbar container">
    <a href="index.php" class="logo">Flavorful</a>
    
    <!-- Mobile Menu Toggle Button -->
    <div class="menu-toggle"><i class="bx bx-menu"></i></div>

    <nav class="nav-menu">';
    
    foreach ($navPages as $label => $url) {
        $isActive = $currentPage === $url ? 'active' : '';
        echo '      <a href="' . htmlspecialchars($url) . '" class="' . $isActive . '">' . htmlspecialchars($label) . '</a>';
    }
    
    echo '    </nav>';

    // Authentication links and profile
    echo '    <div class="auth-links">';
    if (!$authUser) {
        echo '      <a href="register.php" class="signup-link">Register</a>';
        echo '      <a href="login.php" class="login-link">Login</a>';
    } else {
        echo '      <div class="profile-icon">';
        // Link to the new balance page
        echo "        <a href=\"balance.php\" class=\"profile-btn\" title=\"View Balance/Orders\">"; 
        echo "          <span>$initials</span>";
        echo '        </a>';
        echo '        <form method="POST" style="display:inline;">';
        echo '          <button type="submit" name="logout" title="Logout">Logout</button>';
        echo '        </form>';
        echo '      </div>';
    }
    echo '    </div>';
    echo '  </div>';
    echo '</header>';

    // Add required JS for mobile menu functionality
    echo '<script>
        const menuToggle = document.querySelector(\'.menu-toggle\');
        const navMenu = document.querySelector(\'.nav-menu\');

        if (menuToggle && navMenu) {
            menuToggle.addEventListener(\'click\', function() {
                navMenu.classList.toggle(\'active\');
            });
            // Close menu when a link is clicked
            document.querySelectorAll(\'.nav-menu a\').forEach(link => {
                link.addEventListener(\'click\', () => {
                    if (window.innerWidth <= 900) {
                        navMenu.classList.remove(\'active\');
                    }
                });
            });
        }
    </script>';
}

// 5. CUSTOM MODAL FUNCTION (Replaces alert() and confirm())
function renderModal() {
    echo '
    <div id="customModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 25px; border-radius: 8px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);">
            <h4 id="modalTitle" style="margin-bottom: 15px; color: var(--secondary-color);"></h4>
            <p id="modalMessage" style="margin-bottom: 20px; color: #555;"></p>
            <button class="btn" onclick="document.getElementById(\'customModal\').style.display = \'none\';" style="padding: 8px 20px; font-size: 1em;">OK</button>
        </div>
    </div>
    <script>
        function showModal(message, title = "Notification") {
            document.getElementById("modalTitle").innerText = title;
            document.getElementById("modalMessage").innerText = message;
            document.getElementById("customModal").style.display = "flex";
        }
    </script>';
}
?>