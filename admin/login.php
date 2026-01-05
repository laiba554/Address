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

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            background: linear-gradient(135deg, #eaf5f1, #d7efe6);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 42px 38px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(6, 60, 46, 0.18);
            border: 1px solid #e3ebe7;
        }

        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            text-align: center;
            color: #063c2e;
            margin-bottom: 4px;
            letter-spacing: 0.8px;
        }

        .brand-subtitle {
            text-align: center;
            font-size: 14px;
            color: #6f8f84;
            margin-bottom: 28px;
        }

        .form-label {
            color: #063c2e;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            border-radius: 8px;
            padding: 11px 12px;
            border: 1px solid #cfded8;
            background-color: #f9fcfb;
        }

        .form-control:focus {
            border-color: #0b6b4f;
            box-shadow: 0 0 0 2px rgba(11, 107, 79, 0.15);
        }

        .btn-login {
            background: linear-gradient(135deg, #0b6b4f, #158f6b);
            color: #ffffff;
            font-weight: 500;
            border-radius: 8px;
            padding: 12px;
            border: none;
            letter-spacing: 0.4px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #158f6b, #0b6b4f);
            color: #ffffff;
        }

        .error-msg {
            background-color: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            font-size: 14px;
            margin-bottom: 18px;
            border: 1px solid #f1aeb5;
        }

        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: #7a8f86;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="brand-title">Address Jewelers</div>
    <div class="brand-subtitle">Admin Control Panel</div>

    <?php if (!empty($error)): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-login w-100">
            Secure Login
        </button>
    </form>

    <div class="footer-text">
        &copy; 2025 Address Jewelers · Authorized Access Only
    </div>

</div>

</body>
</html>
