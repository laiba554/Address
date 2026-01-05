<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

$customer_id = $_SESSION['customer_id'];

/* GET CUSTOMER DETAILS */
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

/* GET CART */
$stmt = $conn->prepare("SELECT * FROM cart WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

$cart_id = $cart['cart_id'] ?? null;

if (!$cart_id) {
    die("Your cart is empty!");
}

/* FETCH CART ITEMS */
$stmt = $conn->prepare("
    SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.price, p.product_name
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.product_id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
    die("Your cart is empty!");
}

/* PLACE ORDER */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {

    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount) VALUES (?, ?)");
    $stmt->execute([$customer_id, $total]);
    $order_id = $conn->lastInsertId();

    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
    }

    $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$cart_id]);

    header("Location: orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout | Address Jewelers</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* ================= NAVBAR ================= */
nav.navbar {
    background: #0b6b4f;
    padding: 12px 40px;
    position: fixed;   /* FIXED TOP */
    top: 0;            /* STICK TO TOP */
    width: 100%;       /* FULL WIDTH */
    z-index: 1030;     /* ABOVE OTHER ELEMENTS */
}

.navbar-brand {
    font-weight: 700;
    font-size: 22px;
    color: #fff;
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

/* =================================================
   EMERALD • PEARL — PROFESSIONAL CHECKOUT UI
================================================= */

body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(180deg, #F7F9F9, #EFEFEF);
    padding-top: 80px; /* OFFSET FOR FIXED NAVBAR */
    color: #1C1C1C;
}

.container {
    max-width: 1000px;
}

/* Page Title */
h2 {
    text-align: center;
    margin-bottom: 45px;
    font-weight: 600;
    letter-spacing: 0.8px;
    color: #0F3D3E;
}

/* Card Sections */
.customer-info,
.cart-table,
.checkout-btn {
    background: #FFFFFF;
    border-radius: 18px;
    padding: 25px 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(15, 61, 62, 0.12);
}

/* Section Headings */
.customer-info h5 {
    color: #0F3D3E;
    margin-bottom: 18px;
    font-weight: 600;
}

/* Customer Info */
.customer-info p {
    margin-bottom: 8px;
    font-size: 15px;
}

/* Table */
table th {
    background: #0F3D3E;
    color: #FFFFFF;
    text-align: center;
    padding: 14px;
}

table td {
    text-align: center;
    padding: 13px;
}

/* Place Order Button */
.btn-place-order {
    background: linear-gradient(135deg, #1E5F60, #3A8F8A);
    color: #FFFFFF;
    font-weight: 500;
    border-radius: 40px;
    padding: 14px;
    border: none;
}
</style>
</head>

<body>

<!-- FIXED TOP NAVBAR -->
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

<div class="container">
    <h2>Checkout</h2>

    <div class="customer-info">
        <h5>Customer Information</h5>
        <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']); ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($customer['address']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']); ?></p>
        <p><strong>Cell Phone:</strong> <?= htmlspecialchars($customer['cell_phone']); ?></p>
    </div>

    <div class="cart-table table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price ($)</th>
                    <th>Quantity</th>
                    <th>Total ($)</th>
                </tr>
            </thead>
            <tbody>
                <?php $grandTotal = 0; ?>
                <?php foreach ($items as $item): ?>
                    <?php
                        $total = $item['price'] * $item['quantity'];
                        $grandTotal += $total;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']); ?></td>
                        <td><?= number_format($item['price'], 2); ?></td>
                        <td><?= $item['quantity']; ?></td>
                        <td><?= number_format($total, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end">Grand Total</td>
                    <td><?= number_format($grandTotal, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="checkout-btn">
        <form method="post">
            <button type="submit" name="place_order" class="btn btn-place-order btn-lg w-100">
                Place Order
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
