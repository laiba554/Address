<?php
session_start();
require_once __DIR__ . "/../config/db.php";

/* ---------------------------------
   FETCH FEATURED PRODUCTS (LATEST 5)
--------------------------------- */
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jenny Store</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
            margin: 0;
        }

        /* NAVBAR */
        nav {
            background: #1e1e2f;
            padding: 14px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .logo {
            color: #fff;
            font-weight: 600;
            font-size: 20px;
            text-decoration: none;
        }

        nav a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-size: 14px;
        }

        nav a:hover {
            text-decoration: underline;
        }

        /* HERO */
        .hero {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #fbc2eb, #a6c1ee);
            color: #1e1e2f;
        }

        .hero h1 {
            margin-bottom: 10px;
        }

        /* PRODUCTS */
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .product {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            text-align: center;
        }

        .product img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product h4 {
            margin: 10px 0 5px;
            font-size: 16px;
        }

        .product p {
            font-size: 14px;
            margin: 4px 0;
        }

        .product a {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 14px;
            background: #1e1e2f;
            color: #fff;
            border-radius: 6px;
            font-size: 13px;
            text-decoration: none;
        }

        .product a:hover {
            background: #000;
        }

        /* FOOTER */
        footer {
            background: #1e1e2f;
            color: #ccc;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            margin-top: 60px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <a href="/" class="logo">Jenny Store</a>
    <div>
        <a href="/">Home</a>
        <a href="/customer/products.php">Products</a>

        <?php if (isset($_SESSION['customer_id'])): ?>
            <a href="../customer/cart.php">Cart</a>
            <a href="../customer/orders.php">My Orders</a>
            <a href="../public/logout.php">Logout</a>
        <?php else: ?>
            <a href="../customer/login.php">Login</a>
            <a href="../customer/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <h1>Welcome to Jenny Store</h1>
    <p>Explore elegant imitation & Skin Care</p>
</section>

<!-- FEATURED PRODUCTS -->
<div class="container">
    <h2>Featured Products</h2>

    <div class="products">
        <?php foreach ($featured as $p): ?>
            <div class="product">
                <?php if (!empty($p['image_url'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($p['image_url']); ?>" alt="<?= htmlspecialchars($p['product_name']); ?>">
                <?php endif; ?>

                <h4><?= htmlspecialchars($p['product_name']); ?></h4>
                <p><?= htmlspecialchars($p['category_name']); ?></p>
                <p><strong>Rs. <?= number_format($p['price'], 2); ?></strong></p>

                <a href="../customer/cart.php?add=<?= $p['product_id']; ?>">
                    Add to Cart
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- FOOTER -->
<footer>
    © <?= date('Y'); ?> Jenny Store. All Rights Reserved.
</footer>

</body>
</html>
