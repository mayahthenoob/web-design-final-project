<?php
session_start();

// Standardized Database Credentials for InfinityFree
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
    die("Database connection failed. Please try again later.");
}

// Check if user is logged in
$authUser = $_SESSION['authUser'] ?? null;

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: prices.php');
    exit;
}

// Fetch products from database
try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY name ASC');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    error_log("Product fetch failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flavorful - Prices</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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

        /* Prices Section */
        .prices-section {
            padding: 60px;
            text-align: center;
            flex-grow: 1;
        }
        .prices-section h2 {
            font-size: 2.5em;
            color: #d97706;
            margin-bottom: 10px;
        }
        .prices-section .sub-text {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 40px;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background: #f9f9f9;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            text-align: left;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            background: #fef3c7;
        }
        .card-content {
            padding: 20px;
        }
        .card-content span {
            display: block;
            color: #f59e0b;
            font-size: 0.85em;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .card-content h4 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #10b981;
        }
        .card-content p {
            font-size: 0.95em;
            color: #555;
        }

        /* Footer Styles */
        footer {
            text-align: center;
            padding: 20px;
            background: #111;
            color: #eee;
            margin-top: auto;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-direction: column;
                align-items: flex-start;
            }
            nav { width: 100%; justify-content: space-between; margin-top: 10px; }
            .prices-section {
                padding: 40px 20px;
            }
            .card-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo">Flavorful</a>
        <nav>
            <a href="about.php">About</a>
            <a href="prices.php" style="background-color: #f0f0f0;">Prices</a>
            <a href="socials.php">Socials</a>
            <a href="workers.php">Workers</a>
            <a href="buy-now.php">Order</a>

            <?php if (!$authUser): ?>
                <a href="register.php" class="signup-link">Sign Up</a>
                <a href="login.php" class="login-link">Login</a>
            <?php else: ?>
                <div class="profile-icon">
                    <!-- Removed link to balance.php -->
                    <a href="buy-now.php" class="profile-btn" title="View Order Options">
                        <span><?= htmlspecialchars(substr($authUser['username'], 0, 2)); ?></span>
                    </a>
                    <form method="POST" style="display:inline;">
                        <button name="logout">Logout</button>
                    </form>
                </div>
            <?php endif; ?>
        </nav>
    </header>

    <section class="prices-section">
        <h2>Our Product Prices</h2>
        <p class="sub-text">Delicious pennacools in various flavors and sizes. Prices are in XCD.</p>
        <div class="card-container">
            <?php if (empty($products)): ?>
                <p style="grid-column: 1 / -1;">No products found in the database. Please check your SQL connection and schema.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="card">
                        <!-- Placeholder image using the product's flavor or name -->
                        <img src="https://placehold.co/300x150/f59e0b/ffffff?text=<?= urlencode(htmlspecialchars($product['name'])); ?>" 
                             alt="<?= htmlspecialchars($product['name']); ?>"
                             onerror="this.src='https://placehold.co/300x150/f59e0b/ffffff?text=<?= urlencode(htmlspecialchars($product['name'])); ?>';">
                        <div class="card-content">
                            <span><?= htmlspecialchars(ucfirst($product['type'])); ?></span>
                            <h4>$<?= number_format($product['price'], 2); ?></h4>
                            <p><?= htmlspecialchars($product['description']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        &copy; 2025 Flavorful. | All rights reserved.
    </footer>
</body>
</html>