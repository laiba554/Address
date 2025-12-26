<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

/* -----------------------------
   TOP 10 BEST-SELLING PRODUCTS
----------------------------- */
$stmt = $conn->query("
    SELECT p.product_name, SUM(oi.quantity) AS total_sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    GROUP BY p.product_id
    ORDER BY total_sold DESC
    LIMIT 10
");
$topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* -----------------------------
   TOP 10 CUSTOMERS BY PURCHASES
----------------------------- */
$stmt = $conn->query("
    SELECT c.name, SUM(o.total_amount) AS total_spent
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    GROUP BY c.customer_id
    ORDER BY total_spent DESC
    LIMIT 10
");
$topCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Top 10 Best-Selling Products</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>Product Name</th>
        <th>Total Sold</th>
    </tr>
    <?php foreach ($topProducts as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['product_name']); ?></td>
            <td><?= $p['total_sold']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>

<h2>Top 10 Customers by Purchases</h2>
<table border="1" cellpadding="8">
    <tr>
        <th>Customer Name</th>
        <th>Total Amount Spent</th>
    </tr>
    <?php foreach ($topCustomers as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['name']); ?></td>
            <td><?= number_format($c['total_spent'], 2); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
