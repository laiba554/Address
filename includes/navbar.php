<?php
// navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand fw-bold" href="dashboard.php">
        Address Jewelers
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="categories.php">Categories</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="products.php">Products</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="customers.php">Customers</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="orders.php">Orders</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="reports.php">Reports</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="backup.php">Backup</a>
            </li>
        </ul>

        <div class="d-flex align-items-center text-white">
            <span class="me-3">
                <?= htmlspecialchars($_SESSION['admin_role'] ?? 'Admin'); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<style>
    /* Remove horizontal scroll caused by navbar */
    body {
        overflow-x: hidden;
    }

    .navbar {
        position: sticky;
        top: 0;
        z-index: 1000;
    }
</style>
