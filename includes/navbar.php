<?php
// navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page for active link
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background: linear-gradient(135deg, #0b6b4f, #158f6b);">
    <div class="container-fluid px-4">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="../uploads/logo.jpeg" alt="Logo" width="40" height="40" class="me-2 rounded-circle shadow-sm">
            <span class="fw-bold fs-5 text-white"></span>
        </a>

        <!-- Toggler for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu Items -->
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <?php
                $navItems = [
                    'dashboard.php' => 'Dashboard',
                    'categories.php' => 'Categories',
                    'products.php' => 'Products',
                    'customers.php' => 'Customers',
                    'orders.php' => 'Orders',
                    'reports.php' => 'Reports',
                    'backup.php' => 'Backup'
                ];

                foreach ($navItems as $file => $label):
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === $file ? 'active fw-bold' : '' ?>" href="<?= $file ?>">
                        <?= $label ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- Admin info & logout -->
            <div class="d-flex align-items-center text-white">
                <span class="me-3">
                    <i class="bi bi-person-circle"></i>
                    <?= htmlspecialchars($_SESSION['admin_role'] ?? 'Admin'); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold">
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Optional Bootstrap Icons CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* Sticky Navbar */
.navbar {
    position: sticky;
    top: 0;
    z-index: 1000;
}

/* Active Link Highlight */
.navbar-nav .nav-link.active {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 8px;
}

/* NavLink Hover Effect */
.navbar-nav .nav-link:hover {
    background: rgba(255, 255, 255, 0.25);
    border-radius: 8px;
    transition: 0.25s;
}

/* Logo Shadow */
.navbar-brand img {
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

/* Responsive Adjustments */
@media(max-width: 991px) {
    .navbar-nav .nav-link {
        padding: 10px 15px;
        text-align: center;
    }
}
</style>
