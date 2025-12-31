<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    /* PLAIN TEXT PASSWORD CHECK (NO HASH) */
    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_role'] = $admin['role'];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Address Jewelers</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #fbc2eb, #a6c1ee);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: rgba(255,255,255,0.15);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
            backdrop-filter: blur(10px);
            color: #fff;
        }

        .login-box h3 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-control {
            border-radius: 6px;
        }

        .btn-login {
            background-color: #ffffff;
            color: #000;
            font-weight: bold;
            border-radius: 6px;
        }

        .btn-login:hover {
            background-color: #000;
            color: #fff;
        }

        .error-msg {
            background-color: #ff4d4d;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h3>Admin Login</h3>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-login w-100 mt-3">
            Login
        </button>
    </form>

    <div class="footer-text">
        &copy; 2025 Address Jewelers
    </div>
</div>

</body>
</html>
