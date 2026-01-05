<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

$customer_id = $_SESSION['customer_id'];

/* -------------------------------------------------
   GET or CREATE CART
------------------------------------------------- */
$stmt = $conn->prepare("SELECT * FROM cart WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    $stmt = $conn->prepare("INSERT INTO cart (customer_id) VALUES (?)");
    $stmt->execute([$customer_id]);
    $cart_id = $conn->lastInsertId();
} else {
    $cart_id = $cart['cart_id'];
}

/* -------------------------------------------------
   ADD PRODUCT TO CART
------------------------------------------------- */
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);

    $stmt = $conn->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?");
    $stmt->execute([$cart_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE cart_item_id = ?");
        $stmt->execute([$item['cart_item_id']]);
    } else {
        $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$cart_id, $product_id]);
    }

    header("Location: cart.php");
    exit;
}

/* -------------------------------------------------
   UPDATE QUANTITY
------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $item_id => $qty) {
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
        $stmt->execute([$qty, $item_id]);
    }
}

/* -------------------------------------------------
   REMOVE ITEM
------------------------------------------------- */
if (isset($_GET['remove'])) {
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_item_id = ?");
    $stmt->execute([intval($_GET['remove'])]);
    header("Location: cart.php");
    exit;
}

/* -------------------------------------------------
   FETCH CART ITEMS
------------------------------------------------- */
$stmt = $conn->prepare("
    SELECT ci.cart_item_id, p.product_name, p.price, ci.quantity
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.product_id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Shopping Cart | Address Jewelers</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* ================= NAVBAR ================= */
nav.navbar {
    background: #0b6b4f;
    padding: 12px 40px;
    position: fixed;       /* FIXED TOP */
    top: 0;                /* STICK TO TOP */
    width: 100%;           /* FULL WIDTH */
    z-index: 1030;         /* ABOVE OTHER ELEMENTS */
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

/* ================= PAGE UI ================= */
body {
    font-family: 'Montserrat', sans-serif;
    background: linear-gradient(180deg, #F7F9F9, #EFEFEF);
    padding-top: 80px; /* OFFSET FOR FIXED NAVBAR */
    color: #1C1C1C;
}

.container {
    max-width: 1100px;
}

h2 {
    text-align: center;
    margin-bottom: 40px;
    font-weight: 600;
    color: #0F3D3E;
}

table {
    background: #FFFFFF;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 35px rgba(15, 61, 62, 0.12);
}

th {
    background: #0F3D3E;
    color: #FFFFFF;
    text-align: center;
    padding: 16px;
}

td {
    text-align: center;
    padding: 14px;
}

input[type=number] {
    width: 75px;
    text-align: center;
    border-radius: 10px;
    border: 1px solid #CFCFCF;
    padding: 7px;
}

.btn {
    border-radius: 40px;
    padding: 10px 28px;
}

.btn-update {
    background: #0F3D3E;
    color: #FFFFFF;
}

.btn-remove {
    background: #F0F0F0;
    color: #1C1C1C;
}

.btn-checkout {
    background: linear-gradient(135deg, #1E5F60, #3A8F8A);
    color: #FFFFFF;
}

.grand-total {
    font-size: 21px;
    font-weight: 600;
    text-align: right;
    margin-top: 25px;
    color: #0F3D3E;
}

.cart-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 35px;
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
    <h2>Your Shopping Cart</h2>

    <form method="post">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price ($)</th>
                        <th>Quantity</th>
                        <th>Total ($)</th>
                        <th>Action</th>
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
                        <td>
                            <input type="number" name="quantity[<?= $item['cart_item_id']; ?>]" value="<?= $item['quantity']; ?>" min="1">
                        </td>
                        <td><?= number_format($total, 2); ?></td>
                        <td>
                            <a href="?remove=<?= $item['cart_item_id']; ?>" class="btn btn-remove btn-sm">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="grand-total">
            Grand Total: $<?= number_format($grandTotal, 2); ?>
        </div>

        <div class="cart-actions">
            <button type="submit" name="update" class="btn btn-update">Update Cart</button>
            <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
