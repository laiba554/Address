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

    <!-- Google Fonts & Bootstrap -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Base */
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7f9;
            margin: 0;
            color: #1e1e2f;
        }

        a {
            text-decoration: none;
        }

        /* ================= NAVBAR ================= */
        nav.navbar {
            background: #0b6b4f;
            padding: 12px 40px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 22px;
            color: #fff !important;
        }

        .navbar-brand img {
            border-radius: 50%;
            background: #fff;
            padding: 2px;
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .navbar-nav .nav-link {
            color: #fff;
            font-weight: 500;
            margin-right: 15px;
            transition: 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: #ffd700;
        }

        /* HERO */
        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(135deg, #c8b273, #e8dcc3);
            color: #1e1e2f;
            border-radius: 0 0 50% 50% / 0 0 15% 15%;
            margin-bottom: 60px;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .hero p {
            font-size: 20px;
            font-weight: 500;
        }

        /* FEATURED PRODUCTS */
        .container h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            color: #0b6b4f;
            text-align: center;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        .product {
            background: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 12px 25px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .product:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 35px rgba(0,0,0,0.15);
        }

        .product img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .product h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .product p {
            font-size: 14px;
            margin: 3px 0;
            color: #555;
        }

        .product strong {
            color: #0b6b4f;
            font-size: 16px;
        }

        .product a.btn {
            display: inline-block;
            margin-top: 12px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #0b6b4f, #158f6b);
            color: #fff;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s;
        }

        .product a.btn:hover {
            background: linear-gradient(135deg, #158f6b, #0b6b4f);
            color: #fff;
        }

        /* FOOTER */
        footer {
            background: #0b6b4f;
            color: #f0f0f0;
            text-align: center;
            padding: 20px 0;
            margin-top: 60px;
            font-size: 14px;
        }

        /* RESPONSIVE */
        @media(max-width:768px) {
            .hero h1 { font-size: 36px; }
            .hero p { font-size: 18px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="../public/index.php">
        <img src="../uploads/logo.jpeg" alt="Logo">
        Jenny Store
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon" style="color:#fff;"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="../public/index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="../customer/products.php">Products</a></li>
            <?php if (isset($_SESSION['customer_id'])): ?>
                <li class="nav-item"><a class="nav-link" href="../customer/cart.php">Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="../customer/orders.php">My Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="../public/logout.php">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link" href="../customer/login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="../customer/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Welcome to Jenny Store</h1>
    <p>Elegant Imitation Jewelry & Skincare Essentials</p>
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
                <a class="btn" href="../customer/cart.php?add=<?= $p['product_id']; ?>">Add to Cart</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- FOOTER -->
<footer>
    © <?= date('Y'); ?> Jenny Store. All Rights Reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>