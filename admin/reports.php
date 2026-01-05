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

<!-- Google Font (same as other admin pages for navbar consistency) -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f7f5;
    color: #063c2e;
}

/* Heading */
h2 {
    font-weight: 700;
    font-size: 26px;
    margin-bottom: 20px;
    background: linear-gradient(90deg, #0b6b4f, #158f6b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Report Card */
.report-box {
    background: #ffffff;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 18px 45px rgba(0,0,0,0.07);
    margin-bottom: 40px;
    transition: 0.3s;
}

.report-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.1);
}

/* Table Styling */
table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

table th {
    background: #d7efe6;
    color: #0b6b4f;
    font-weight: 600;
    text-align: center;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    padding: 12px;
}

table td {
    padding: 12px;
    vertical-align: middle;
    text-align: center;
}

table tbody tr:nth-child(even) {
    background: #f9fcfb;
}

table tbody tr:hover {
    background: #e0f1ea;
    transform: scale(1.01);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.rank {
    font-weight: 700;
    color: #0b6b4f;
}

/* Responsive */
@media(max-width: 768px) {
    h2 { font-size: 22px; }
    table th, table td { font-size: 13px; padding: 8px; }
}
</style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="container mt-5 mb-5">

    <!-- TOP PRODUCTS -->
    <div class="report-box table-responsive">
        <h2>Top 10 Best-Selling Products</h2>

        <table class="table align-middle">
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

        <table class="table align-middle">
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
                    <td>₨ <?= number_format($c['total_spent'], 2) ?></td>
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
