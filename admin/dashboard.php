<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

/* Fetch Dashboard Statistics */
$totalCategories = $conn->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalProducts   = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCustomers  = $conn->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$totalOrders     = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Jewelry Store</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Internal CSS -->
    <style>
        body {
            background-color: #1E1E2F;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        .dashboard-container {
            margin-top: 40px;
        }

        h2 {
            color: #FFD700;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: #2C2C3E;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 36px;
            color: #FFD700;
        }

        .stat-card p {
            margin: 0;
            font-size: 16px;
            color: #ccc;
        }

        .nav-card {
            background-color: #2C2C3E;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            transition: 0.2s;
        }

        .nav-card a {
            color: #FFD700;
            font-weight: bold;
            text-decoration: none;
            display: block;
        }

        .nav-card:hover {
            background-color: #3a3a55;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<?php include "../includes/navbar.php"; ?>

<div class="container dashboard-container">

    <h2 class="text-center">Admin Dashboard</h2>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card">
                <h3><?= (int)$totalCategories ?></h3>
                <p>Total Categories</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3><?= (int)$totalProducts ?></h3>
                <p>Total Products</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3><?= (int)$totalCustomers ?></h3>
                <p>Total Customers</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3><?= (int)$totalOrders ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="nav-card">
                <a href="categories.php">Manage Categories</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="nav-card">
                <a href="products.php">Manage Products</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="nav-card">
                <a href="customers.php">View Customers</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="nav-card">
                <a href="orders.php">View Orders</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="nav-card">
                <a href="reports.php">Reports</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="nav-card">
                <a href="backup.php">Database Backup</a>
            </div>
        </div>
    </div>

</div>

<!-- Footer -->
<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
