<?php
session_start();
require_once "../config/db.php";

// Fetch featured products (latest 5 products)
$stmt = $conn->query("
    SELECT p.*, c.category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.status = 'Available'
    ORDER BY p.created_at DESC
    LIMIT 5
");
$featured = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pascal Imitation Jewellery</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; }
        nav { background: #222; padding: 10px; }
        nav a { color: #fff; margin-right: 15px; text-decoration: none; }
        .product { border: 1px solid #ccc; padding: 10px; margin: 10px; display: inline-block; width: 180px; vertical-align: top; }
        .product img { width: 100%; height: 150px; object-fit: cover; }
    </style>
</head>
<body>

<nav>
    <a href="index.php">Home</a>
    <a href="customer/products.php">Products</a>
    <?php if(isset($_SESSION['customer_id'])): ?>
        <a href="customer/cart.php">Cart</a>
        <a href="customer/orders.php">My Orders</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="customer/login.php">Login</a>
        <a href="customer/register.php">Register</a>
    <?php endif; ?>
</nav>

<h1>Welcome to Pascal Imitation Jewellery</h1>
<p>Browse our collection of beautiful imitation jewellery and cosmetics.</p>

<h2>Featured Products</h2>
<div>
    <?php foreach($featured as $p): ?>
        <div class="product">
            <?php if($p['image_url']): ?>
                <img src="../uploads/<?= $p['image_url']; ?>" alt="<?= htmlspecialchars($p['product_name']); ?>">
            <?php endif; ?>
            <h4><?= htmlspecialchars($p['product_name']); ?></h4>
            <p>Category: <?= htmlspecialchars($p['category_name']); ?></p>
            <p>Price: <?= $p['price']; ?></p>
            <a href="customer/cart.php?add=<?= $p['product_id']; ?>">Add to Cart</a>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
