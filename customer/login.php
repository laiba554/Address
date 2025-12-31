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

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Internal CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e1e2f, #3a3a5f);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }

        .login-card h3 {
            text-align: center;
            font-weight: 600;
            margin-bottom: 25px;
            color: #1e1e2f;
        }

        .form-control {
            height: 45px;
            border-radius: 8px;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #1e1e2f;
            box-shadow: none;
        }

        .btn-login {
            background-color: #1e1e2f;
            color: #fff;
            font-weight: 500;
            border-radius: 8px;
            padding: 10px;
            width: 100%;
        }

        .btn-login:hover {
            background-color: #000;
        }

        .error-msg {
            background-color: #ff4d4d;
            color: #fff;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .register-link a {
            color: #1e1e2f;
            font-weight: 500;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .brand-text {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h3>Customer Login</h3>

    <?php if (!empty($error)): ?>
        <div class="error-msg">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-login mt-2">
            Login
        </button>
    </form>

    <div class="register-link">
        New customer?
        <a href="register.php">Create an account</a>
    </div>

    <div class="brand-text">
        © 2025 Address Jewelers
    </div>
</div>

</body>
</html>
