<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
    $stmt->execute([$email]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customer && password_verify($password, $customer['password'] ?? "")) {
        $_SESSION['customer_id'] = $customer['customer_id'];
        $_SESSION['customer_name'] = $customer['name'];
        header("Location: products.php");
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Login | Address Jewelers</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ================= NAVBAR ================= */
nav.navbar {
    background: #0b6b4f;
    padding: 12px 40px;
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 1030;
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

/* ================= LOGIN PAGE ================= */
body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    padding-top: 80px;
    background:
        radial-gradient(circle at top, rgba(15,61,46,0.06), transparent 45%),
        #f4f7f5;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #2c2c2c;
    flex-direction: column;
}

.login-card {
    background-color: #ffffff;
    width: 100%;
    max-width: 430px;
    border-radius: 16px;
    padding: 40px 38px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.10);
    border: 1px solid #e1e7e4;
    position: relative;
}

.login-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    height: 6px;
    width: 100%;
    background: linear-gradient(90deg, #0f3d2e, #2f6f5c);
    border-radius: 16px 16px 0 0;
}

.login-card h3 {
    text-align: center;
    font-weight: 600;
    margin-bottom: 10px;
    color: #0f3d2e;
}

.subtitle {
    text-align: center;
    font-size: 13px;
    color: #777;
    margin-bottom: 28px;
}

.form-label {
    font-size: 13px;
    font-weight: 500;
    color: #444;
    margin-bottom: 6px;
}

.form-control {
    height: 48px;
    border-radius: 10px;
    font-size: 14px;
    border: 1px solid #d9e0dc;
    padding-left: 14px;
}

.form-control:focus {
    border-color: #0f3d2e;
    box-shadow: 0 0 0 0.15rem rgba(15, 61, 46, 0.12);
}

.btn-login {
    background: linear-gradient(135deg, #0b6b4f, #158f6b);
    color: #ffffff;
    font-weight: 500;
    border-radius: 30px;
    padding: 12px;
    width: 100%;
    border: none;
    transition: 0.25s;
}

.btn-login:hover {
    background: linear-gradient(135deg, #158f6b, #0b6b4f);
}

.error-msg {
    background-color: #b00020;
    color: #ffffff;
    padding: 12px;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 18px;
    text-align: center;
}

.divider {
    height: 1px;
    background: #e6ece9;
    margin: 24px 0 18px;
}

.register-link {
    text-align: center;
    font-size: 14px;
    color: #555;
}

.register-link a {
    color: #0f3d2e;
    font-weight: 500;
    text-decoration: none;
}

.register-link a:hover {
    text-decoration: underline;
}

.brand-text {
    text-align: center;
    margin-top: 22px;
    font-size: 12px;
    color: #888;
}

/* ================= FOOTER ================= */
footer {
    background: #0b6b4f;
    color: #f0f0f0;
    text-align: center;
    padding: 20px 0;
    margin-top: 60px;
    font-size: 14px;
    width: 100%;
}
</style>
</head>

<body>

<!-- ================= NAVBAR (SAME AS OTHER PAGES) ================= -->
<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="/">
        <img src="../uploads/logo.jpeg"
             alt="Logo"
             width="40"
             height="40"
             style="
                border-radius:50%;
                object-fit:contain;
                background:#ffffff;
                padding:2px;
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
                margin-right:8px;
             ">
        Jenny Store
    </a>

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

<div class="login-card">
    <h3>Customer Login</h3>
    <div class="subtitle">Access your jewelry collection</div>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn-login mt-2">Login</button>
    </form>

    <div class="divider"></div>

    <div class="register-link">
        New customer?
        <a href="register.php">Create an account</a>
    </div>

    <div class="brand-text">
        © 2025 Address Jewelers
    </div>
</div>

<footer>
    © <?= date('Y'); ?> Jenny Store. All Rights Reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>