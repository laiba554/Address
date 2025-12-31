<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

$customer_id = $_SESSION['customer_id'];

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
    unset($_SESSION['order_success']); // remove after showing once
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f6fa;
        }

        .orders-container {
            max-width: 1100px;
            margin: 40px auto;
        }

        .page-title {
            font-weight: 600;
            color: #1e1e2f;
            margin-bottom: 25px;
        }

        .order-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .Pending { background-color: #ffeaa7; color: #856404; }
        .Confirmed { background-color: #d4edda; color: #155724; }
        .Delivered { background-color: #cce5ff; color: #004085; }
        .Cancelled { background-color: #f8d7da; color: #721c24; }

        .items-list {
            list-style: none;
            padding-left: 0;
        }

        .items-list li {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #ddd;
            padding: 6px 0;
            font-size: 14px;
        }

        .total-amount {
            font-weight: 600;
            font-size: 16px;
            text-align: right;
            margin-top: 10px;
        }

        .empty-orders {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

<div class="orders-container">

    <h3 class="page-title">My Orders</h3>

    <!-- SUCCESS MESSAGE -->
    <?php if ($showSuccess): ?>
        <div class="alert alert-success alert-dismissible fade show">
            🎉 Your order has been placed successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="empty-orders">
            <h5>You have not placed any orders yet</h5>
            <p class="text-muted">Browse our collection and place your first order.</p>
            <a href="products.php" class="btn btn-dark mt-2">Shop Now</a>
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
                        <?= $o['status']; ?>
                    </div>
                </div>

                <ul class="items-list">
                    <?php foreach ($items as $item): ?>
                        <li>
                            <span><?= htmlspecialchars($item['product_name']); ?> (x<?= $item['quantity']; ?>)</span>
                            <span>₹<?= number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="total-amount">
                    Total: ₹<?= number_format($o['total_amount'], 2); ?>
                </div>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
