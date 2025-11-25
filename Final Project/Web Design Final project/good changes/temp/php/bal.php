<?php
// ======================================
// balance.php - User Account/Balance Page
// ======================================

session_start();

// 🔧 InfinityFree Database Credentials (REQUIRED)
$host = 'YOUR_DB_HOST';              // Example: sql303.epizy.com
$db   = 'YOUR_DB_NAME';              // Example: epiz_12345678_flavorful
$user = 'YOUR_DB_USERNAME';          // Example: epiz_12345678
$password = 'YOUR_DB_PASSWORD';      // Found in Control Panel

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$authUser = $_SESSION['authUser'] ?? null;

if (!$authUser) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Fetch user data
try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$authUser['id']]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    $balanceStmt = $pdo->prepare('SELECT * FROM account_balance WHERE user_id = ?');
    $balanceStmt->execute([$authUser['id']]);
    $balance = $balanceStmt->fetch(PDO::FETCH_ASSOC);

    $ordersStmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 10');
    $ordersStmt->execute([$authUser['id']]);
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $userData = [];
    $balance = [];
    $orders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - My Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
/* (CSS unchanged) */
<?php echo file_get_contents("balance-styles.css"); ?>
</style>

</head>

<body>
<!-- (HTML unchanged — this keeps full functionality & UI) -->
<?php include("balance-content.php"); ?>

<footer>&copy; 2025 Flavorful. | All rights reserved.</footer>
</body>
</html>
