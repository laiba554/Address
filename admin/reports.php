<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

/* -----------------------------
   TOP 10 BEST-SELLING PRODUCTS
----------------------------- */
$topProducts = $conn->query("
    SELECT p.product_name, SUM(oi.quantity) AS total_sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    GROUP BY p.product_id
    ORDER BY total_sold DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

/* -----------------------------
   TOP 10 CUSTOMERS BY PURCHASE
----------------------------- */
$topCustomers = $conn->query("
    SELECT c.name, SUM(o.total_amount) AS total_spent
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    GROUP BY c.customer_id
    ORDER BY total_spent DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | Admin</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Internal CSS -->
    <style>
        body {
            background-color: #1E1E2F;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        h2 {
            color: #FFD700;
            margin-bottom: 20px;
        }

        .report-box {
            background-color: #2C2C3E;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            margin-bottom: 40px;
        }

        table th {
            background-color: #3a3a55;
            color: #FFD700;
            white-space: nowrap;
        }

        table td {
            vertical-align: middle;
        }

        .rank {
            font-weight: bold;
            color: #FFD700;
        }
    </style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="container mt-5 mb-5">

    <!-- TOP PRODUCTS -->
    <div class="report-box table-responsive">
        <h2>Top 10 Best-Selling Products</h2>

        <table class="table table-dark table-bordered table-hover">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Product Name</th>
                    <th>Total Units Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($topProducts as $p): ?>
                <tr>
                    <td class="rank">#<?= $rank++ ?></td>
                    <td><?= htmlspecialchars($p['product_name']) ?></td>
                    <td><?= (int)$p['total_sold'] ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($topProducts)): ?>
                <tr>
                    <td colspan="3" class="text-center">No sales data available</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- TOP CUSTOMERS -->
    <div class="report-box table-responsive">
        <h2>Top 10 Customers by Purchases</h2>

        <table class="table table-dark table-bordered table-hover">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Customer Name</th>
                    <th>Total Amount Spent</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; foreach ($topCustomers as $c): ?>
                <tr>
                    <td class="rank">#<?= $rank++ ?></td>
                    <td><?= htmlspecialchars($c['name']) ?></td>
                    <td>₹ <?= number_format($c['total_spent'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($topCustomers)): ?>
                <tr>
                    <td colspan="3" class="text-center">No customer data available</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
