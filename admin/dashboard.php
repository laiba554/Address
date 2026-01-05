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

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f7f5;
        color: #2c2c2c;
    }

    .dashboard-wrapper {
        max-width: 1280px;
        margin: 40px auto;
        padding: 0 15px;
    }

    .dashboard-header {
        margin-bottom: 35px;
    }

    .dashboard-header h2 {
        font-size: 32px;
        font-weight: 700;
        background: linear-gradient(90deg, #0f3d2e, #2f6f5c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 6px;
    }

    .dashboard-header p {
        font-size: 14px;
        color: #6b7d77;
        margin: 0;
    }

    /* Stats Cards */
    .stat-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 30px 20px;
        text-align: center;
        border: 1px solid #e2ece7;
        box-shadow: 0 18px 45px rgba(0,0,0,0.07);
        transition: all 0.25s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 28px 65px rgba(0,0,0,0.1);
    }

    .stat-value {
        font-size: 38px;
        font-weight: 700;
        color: #0f3d2e;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 14px;
        color: #6b7d77;
        font-weight: 500;
    }

    /* Navigation Section */
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #0f3d2e;
        margin: 45px 0 18px;
    }

    .nav-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 28px 20px;
        text-align: center;
        border: 1px solid #e2ece7;
        box-shadow: 0 16px 40px rgba(0,0,0,0.06);
        transition: all 0.25s ease;
        height: 100%;
    }

    .nav-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 26px 60px rgba(0,0,0,0.1);
    }

    .nav-card a {
        text-decoration: none;
        font-weight: 600;
        color: #0f3d2e;
        font-size: 15px;
        display: block;
    }

    .nav-card span {
        display: block;
        font-size: 13px;
        color: #6b7d77;
        margin-top: 6px;
    }

    @media (max-width: 768px) {
        .dashboard-header h2 {
            font-size: 26px;
        }

        .stat-value {
            font-size: 32px;
        }
    }
</style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-wrapper">

    <div class="dashboard-header text-center">
        <h2>Admin Dashboard</h2>
        <p>Overview & management controls for your jewelry store</p>
    </div>

    <!-- Statistics -->
    <div class="row g-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-value"><?= (int)$totalCategories ?></div>
                <div class="stat-label">Categories</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-value"><?= (int)$totalProducts ?></div>
                <div class="stat-label">Products</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-value"><?= (int)$totalCustomers ?></div>
                <div class="stat-label">Customers</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-value"><?= (int)$totalOrders ?></div>
                <div class="stat-label">Orders</div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="section-title text-center">Quick Management</div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="nav-card">
                <a href="categories.php">Manage Categories</a>
                <span>Create & organize product categories</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="nav-card">
                <a href="products.php">Manage Products</a>
                <span>Add, edit & manage inventory</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="nav-card">
                <a href="customers.php">View Customers</a>
                <span>Customer accounts & details</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="nav-card">
                <a href="orders.php">View Orders</a>
                <span>Track & manage customer orders</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="nav-card">
                <a href="reports.php">Reports</a>
                <span>Sales & performance insights</span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="nav-card">
                <a href="backup.php">Database Backup</a>
                <span>Secure & manage system backups</span>
            </div>
        </div>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
