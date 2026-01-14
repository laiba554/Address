<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

$customer_id   = $_SESSION['customer_id'];
$customer_name = $_SESSION['customer_name'] ?? "Customer";

/* FETCH CUSTOMER ORDERS */
$stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE customer_id = ?
    ORDER BY order_date DESC
");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* FLASH MESSAGE */
$showSuccess = false;
if (!empty($_SESSION['order_success'])) {
    $showSuccess = true;
    unset($_SESSION['order_success']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders | Address Jewelers</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f7f5;
    color: #2c2c2c;
    padding-top: 90px;
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
    color: #fff !important;
    font-weight: 500;
    margin-right: 15px;
    transition: 0.3s;
}

.navbar-nav .nav-link:hover {
    color: #ffd700 !important;
}

/* PAGE */
.orders-container {
    max-width: 1100px;
    margin: auto;
    padding: 0 15px 120px; /* bottom padding for footer */
}

.page-title {
    font-weight: 700;
    font-size: 32px;
    text-align: center;
    margin-bottom: 40px;
    background: linear-gradient(90deg, #0f3d2e, #2f6f5c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.alert-success {
    background: #e0f4ea;
    color: #0f3d2e;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(15,61,46,0.15);
    padding: 15px 22px;
    margin-bottom: 30px;
    font-weight: 500;
}

.order-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 25px 30px;
    margin-bottom: 25px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.08);
    border: 1px solid #e0ebe5;
    transition: 0.3s;
}

.order-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 30px 70px rgba(0,0,0,0.12);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e6ece9;
    padding-bottom: 12px;
    margin-bottom: 18px;
}

.status {
    padding: 7px 18px;
    border-radius: 25px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
}

.Pending { background: #fff8e5; color: #856404; }
.Confirmed { background: #e6f7ed; color: #0f3d2e; }
.Delivered { background: #e5f1ff; color: #004085; }
.Cancelled { background: #fde8e9; color: #721c24; }

.items-list {
    list-style: none;
    padding-left: 0;
}

.items-list li {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #d9e0dc;
    font-size: 14.5px;
    font-weight: 500;
}

.total-amount {
    font-weight: 700;
    font-size: 17px;
    text-align: right;
    color: #0f3d2e;
}

.empty-orders {
    background: #ffffff;
    padding: 60px 45px;
    border-radius: 22px;
    text-align: center;
    box-shadow: 0 25px 60px rgba(0,0,0,0.08);
}

.btn-shop {
    background: linear-gradient(135deg, #1abc9c, #16a085);
    color: #fff;
    padding: 14px 32px;
    border-radius: 30px;
    font-weight: 600;
    border: none;
}

/* FOOTER */
footer {
    background: #0b6b4f;
    color: #f0f0f0;
    text-align: center;
    padding: 20px 0;
    font-size: 14px;
    position: fixed;
    bottom: 0;
    width: 100%;
}
</style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="/">
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

<div class="orders-container">

    <h3 class="page-title">My Orders</h3>

    <?php if ($showSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Your order has been placed successfully.
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="empty-orders">
            <h5>You have not placed any orders yet</h5>
            <p>Browse our exclusive collection and place your first order.</p>
            <a href="products.php" class="btn btn-shop">Shop Now</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $o): ?>
            <?php
            $stmtItems = $conn->prepare("
                SELECT p.product_name, oi.quantity, oi.price
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = ?
            ");
            $stmtItems->execute([$o['order_id']]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="order-card">
                <div class="order-header">
                    <div>
                        <strong>Order #<?= $o['order_id']; ?></strong><br>
                        <small><?= date("d M Y, h:i A", strtotime($o['order_date'])); ?></small>
                    </div>
                    <div class="status <?= $o['status']; ?>">
                        <?= ucfirst($o['status']); ?>
                    </div>
                </div>

                <ul class="items-list">
                    <?php foreach ($items as $item): ?>
                        <li>
                            <span><?= htmlspecialchars($item['product_name']); ?> (x<?= $item['quantity']; ?>)</span>
                            <span>PKR <?= number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="total-amount">
                    Total: PKR <?= number_format($o['total_amount'], 2); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<!-- FOOTER -->
<footer>
    © <?= date('Y'); ?> Jenny Store. All Rights Reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>