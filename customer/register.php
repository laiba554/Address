<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $work_phone = trim($_POST['work_phone']);
    $cell_phone = trim($_POST['cell_phone']);
    $dob = $_POST['date_of_birth'];
    $remarks = trim($_POST['remarks']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("
            INSERT INTO customers
            (name, address, email, work_phone, cell_phone, date_of_birth, remarks)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $name, $address, $email,
            $work_phone, $cell_phone,
            $dob, $remarks
        ]);

        $_SESSION['customer_id'] = $conn->lastInsertId();
        $_SESSION['customer_name'] = $name;
        header("Location: products.php");
        exit;

    } catch (PDOException $e) {
        $error = "Email already exists";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Registration</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #e4e7eb);
            min-height: 100vh;
        }

        .register-container {
            max-width: 650px;
            margin: 60px auto;
        }

        .register-card {
            background: #fff;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .register-title {
            font-weight: 600;
            color: #1e1e2f;
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
        }

        .form-control, textarea {
            border-radius: 10px;
        }

        .btn-register {
            background: #1e1e2f;
            color: #fff;
            border-radius: 10px;
            padding: 12px;
            font-weight: 500;
        }

        .btn-register:hover {
            background: #000;
        }

        .error-msg {
            background: #ffecec;
            color: #d8000c;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }

        .login-link {
            font-size: 14px;
            text-align: center;
        }

        .login-link a {
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-card">

        <h3 class="register-title text-center mb-4">Create Your Account</h3>

        <?php if ($error): ?>
            <div class="error-msg"><?= $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="row g-3">

                <div class="col-md-12">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2" required></textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Work Phone</label>
                    <input type="text" name="work_phone" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cell Phone</label>
                    <input type="text" name="cell_phone" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Remarks (Optional)</label>
                    <textarea name="remarks" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-12 d-grid mt-3">
                    <button type="submit" class="btn btn-register">
                        Register Account
                    </button>
                </div>
            </div>
        </form>

        <div class="login-link mt-4">
            Already have an account?
            <a href="login.php">Login here</a>
        </div>

    </div>
</div>

</body>
</html>
