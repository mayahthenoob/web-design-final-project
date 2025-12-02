<?php
// ======================================
// delivery.php - Delivery/Checkout Page
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header('Location: delivery.php');
    exit;
}

// Fetch products
try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY name ASC');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flavorful - Delivery</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
/* (CSS unchanged) */
<?php echo file_get_contents("delivery-styles.css"); ?>
</style>

</head>
<body>
<!-- (HTML unchanged — entire page preserved exactly as you provided) -->
<?php include("delivery-content.php"); ?>

<script>
const deliveryProducts = <?php echo json_encode($products); ?>;
/* (JS unchanged) */
</script>

</body>
</html>
