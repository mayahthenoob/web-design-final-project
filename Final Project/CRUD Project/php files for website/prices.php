<?php
require_once 'utils.php'; // Includes session_start(), DB connection, auth, and logout

// Fetch products from database
try {
    $stmt = $pdo->query('SELECT product_id, name, price, description, type FROM products ORDER BY price ASC');
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    error_log("Product fetch failed: " . $e->getMessage());
}

// Group products for display
$groupedProducts = [
    'bulk' => array_filter($products, fn($p) => $p['type'] === 'bulk'),
    'single' => array_filter($products, fn($p) => $p['type'] === 'single'),
];

renderHead('Product Prices');
renderHeader('prices.php', $authUser);
?>

<main class="container" style="padding: 40px 20px;">
    <section class="prices-section">
        <h1 style="font-size: 3em; color: var(--secondary-color); text-align: center; margin-bottom: 10px;">Our Product Prices</h1>
        <p class="sub-text" style="text-align: center; font-size: 1.1em; color: #555; margin-bottom: 40px;">Delicious pennacools in various flavors and sizes. All prices are in XCD (Eastern Caribbean Dollars).</p>
        
        <?php if (empty($products)): ?>
            <p style="text-align: center; font-size: 1.2em; color: #c0392b;">No products found. Please check the database connection and the `products` table in `schema.sql`.</p>
        <?php else: ?>
            <div class="card-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
                
                <?php
                // Function to render a single product card
                $renderCard = function($product) {
                    $bgColor = $product['type'] === 'bulk' ? '#fff7ed' : 'white';
                    $borderColor = $product['type'] === 'bulk' ? 'var(--primary-color)' : '#eee';
                    $shadow = $product['type'] === 'bulk' ? '0 8px 15px rgba(245, 158, 11, 0.2)' : 'var(--shadow-md)';
                    
                    echo '<div class="card" style="border: 1px solid ' . $borderColor . '; padding: 25px; border-radius: 10px; box-shadow: ' . $shadow . '; text-align: center; background: ' . $bgColor . ';">';
                    echo '    <i class="bx ' . ($product['type'] === 'bulk' ? 'bxs-box' : 'bxs-lemon') . '" style="font-size: 3em; color: var(--secondary-color); margin-bottom: 15px;"></i>';
                    echo '    <h3 style="font-size: 1.5em; color: var(--secondary-color); margin-bottom: 5px;">' . htmlspecialchars($product['name']) . '</h3>';
                    echo '    <p style="font-size: 1.8em; font-weight: 700; color: var(--primary-color); margin-bottom: 10px;">$' . number_format($product['price'], 2) . '</p>';
                    echo '    <p style="font-size: 0.9em; color: #666;">' . htmlspecialchars($product['description']) . '</p>';
                    echo '</div>';
                };
                
                ?>
                
                <?php if (!empty($groupedProducts['bulk'])): ?>
                    <h2 style="grid-column: 1 / -1; font-size: 2em; color: var(--secondary-color); margin: 30px 0 15px 0;">Bulk & Wholesale Pricing</h2>
                    <?php array_map($renderCard, $groupedProducts['bulk']); ?>
                <?php endif; ?>
                
                <?php if (!empty($groupedProducts['single'])): ?>
                    <h2 style="grid-column: 1 / -1; font-size: 2em; color: var(--secondary-color); margin: 30px 0 15px 0;">Individual Packs</h2>
                    <?php array_map($renderCard, $groupedProducts['single']); ?>
                <?php endif; ?>

            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="buy-now.php" class="btn" style="padding: 12px 30px; font-size: 1.1em;">Start Your Order Now</a>
            </div>
            
        <?php endif; ?>
    </section>
</main>

<footer>
    &copy; 2025 Flavorful. | All rights reserved.
</footer>
</body>
</html>