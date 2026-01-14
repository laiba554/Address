<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

$customer_name = $_SESSION['customer_name'] ?? "Customer";

/* FETCH CATEGORIES */
$catStmt = $conn->query("SELECT * FROM categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

/* SEARCH & FILTER */
$where = [];
$params = [];

if (!empty($_GET['search'])) {
    $where[] = "p.product_name LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

if (!empty($_GET['category_id'])) {
    $where[] = "p.category_id = ?";
    $params[] = $_GET['category_id'];
}

$sql = "
    SELECT p.*, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.status = 'Available'
";

if ($where) {
    $sql .= " AND " . implode(" AND ", $where);
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products | Jenny Store</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f7f5;
    padding-top: 90px;
    color: #2c2c2c;
}

/* NAVBAR */
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
    font-weight: 700;
    font-size: 22px;
    color: #fff !important;
}

.navbar-nav .nav-link {
    color: #fff !important;
    font-weight: 500;
    margin-right: 15px;
    transition: 0.3s;
}

.navbar-nav .nav-link:hover {
    color: #ffd700 !important;
}

/* PAGE */
.page-container {
    max-width: 1200px;
    margin: auto;
    padding: 0 15px 40px;
}

.page-title {
    font-weight: 700;
    font-size: 32px;
    text-align: center;
    margin-bottom: 35px;
    background: linear-gradient(90deg, #0f3d2e, #2f6f5c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* FILTER BOX */
.filter-box {
    background: #ffffff;
    padding: 22px 25px;
    border-radius: 16px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
    margin-bottom: 35px;
}

.form-label {
    font-weight: 500;
    color: #0f3d2e;
}

/* ================= BUTTONS (From Featured Products Page) ================= */
.btn-search {
    display: inline-block;
    padding: 10px 18px;
    background: linear-gradient(135deg, #0b6b4f, #158f6b);
    color: #fff;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    transition: 0.3s;
    cursor: pointer;
}

.btn-search:hover {
    background: linear-gradient(135deg, #158f6b, #0b6b4f);
    color: #fff;
}

/* PRODUCT CARD */
.product-card {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 18px 45px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 28px 60px rgba(0,0,0,0.12);
}

.product-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

.product-body {
    padding: 18px 15px;
}

.product-name {
    font-weight: 600;
    color: #0f3d2e;
}

.category {
    font-size: 13px;
    color: #777;
}

.price {
    font-weight: 700;
    color: #0f3d2e;
}

.stock {
    font-size: 13px;
    color: #16a085;
}

/* Add to Cart Button Updated */
.btn-cart {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 18px;
    background: linear-gradient(135deg, #0b6b4f, #158f6b);
    color: #fff;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    transition: 0.3s;
    text-align: center;
    cursor: pointer;
    text-decoration: none;
}

.btn-cart:hover {
    background: linear-gradient(135deg, #158f6b, #0b6b4f);
    color: #fff;
}

.no-products {
    background: #ffffff;
    padding: 50px 40px;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
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
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="/">Jenny Store</a>
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

<div class="page-container">

    <h3 class="page-title">Our Products</h3>

    <!-- FILTER -->
    <div class="filter-box">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Search Product</label>
                <input type="text" name="search" class="form-control"
                       value="<?= $_GET['search'] ?? '' ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id']; ?>"
                            <?= (($_GET['category_id'] ?? '') == $cat['category_id']) ? 'selected' : '' ?> >
                            <?= htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn-search">Search</button>
            </div>
        </form>
    </div>

    <!-- PRODUCTS -->
    <div class="row g-4">
        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="no-products">
                    <h5>No products found</h5>
                    <p>Try adjusting your search or category.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($products as $p): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card h-100">
                    <img src="<?= $p['image_url'] ? '../uploads/'.$p['image_url'] : '../assets/no-image.png'; ?>"
                         class="product-img">

                    <div class="product-body">
                        <div class="product-name"><?= htmlspecialchars($p['product_name']); ?></div>
                        <div class="category"><?= htmlspecialchars($p['category_name']); ?></div>

                        <div class="price mt-2">Rs. <?= number_format($p['price'], 2); ?></div>
                        <div class="stock">In Stock: <?= $p['stock_quantity']; ?></div>

                        <a href="cart.php?add=<?= $p['product_id']; ?>" class="btn-cart">
                            Add to Cart
                        </a>
                    </div>
                </div>
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
