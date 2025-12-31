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
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            padding-top: 60px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .customer-info, .cart-table, .checkout-btn {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }

        .customer-info p {
            margin-bottom: 8px;
            font-size: 16px;
        }

        table th {
            background-color: #007bff;
            color: #fff;
            text-align: center;
        }

        table td {
            text-align: center;
            vertical-align: middle;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }

        .btn-place-order {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            width: 100%;
        }

        .btn-place-order:hover {
            background-color: #218838;
            color: white;
        }

        @media (max-width: 768px) {
            table td, table th {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

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
        <table class="table table-bordered align-middle">
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
                    <?php $total = $item['price'] * $item['quantity']; $grandTotal += $total; ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']); ?></td>
                        <td><?= number_format($item['price'], 2); ?></td>
                        <td><?= $item['quantity']; ?></td>
                        <td><?= number_format($total, 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Grand Total</strong></td>
                    <td><strong><?= number_format($grandTotal, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="checkout-btn">
        <form method="post">
            <button type="submit" name="place_order" class="btn btn-place-order btn-lg">Place Order</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
