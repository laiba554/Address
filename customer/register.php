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
            (name, address, email, work_phone, cell_phone, date_of_birth, remarks, password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $name, $address, $email,
            $work_phone, $cell_phone,
            $dob, $remarks, $password
        ]);

        $_SESSION['customer_id'] = $conn->lastInsertId();
        $_SESSION['customer_name'] = $name;
        header("Location: products.php");
        exit;

    } catch (PDOException $e) {
        $error = "Email already exists or invalid data";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Registration | Address Jewelers</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f0f5f3, #e0ece7);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .register-container {
        max-width: 700px;
        width: 100%;
    }

    .register-card {
        background: #fff;
        padding: 40px 35px;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .register-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 28px 60px rgba(0,0,0,0.12);
    }

    .register-title {
        font-weight: 700;
        color: #0f3d2e;
        font-size: 28px;
        text-align: center;
        margin-bottom: 30px;
    }

    .form-label {
        font-weight: 500;
        color: #0f3d2e;
    }

    .form-control, textarea {
        border-radius: 12px;
        border: 1px solid #cdd9d6;
        padding: 10px 12px;
        font-size: 14px;
    }

    .form-control:focus, textarea:focus {
        border-color: #16a085;
        box-shadow: 0 0 8px rgba(22,160,133,0.2);
    }

    .btn-register {
        background: linear-gradient(135deg, #1abc9c, #16a085);
        color: #fff;
        font-weight: 600;
        border-radius: 30px;
        padding: 12px 0;
        font-size: 15px;
        text-transform: uppercase;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-register:hover {
        background: linear-gradient(135deg, #16a085, #1abc9c);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(22,160,133,0.35);
    }

    .error-msg {
        background: #ffecec;
        color: #d8000c;
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 500;
    }

    .login-link {
        text-align: center;
        margin-top: 25px;
        font-size: 14px;
        color: #555;
    }

    .login-link a {
        text-decoration: none;
        font-weight: 600;
        color: #16a085;
        transition: all 0.3s;
    }

    .login-link a:hover {
        color: #1abc9c;
    }

    @media (max-width: 576px) {
        .register-card {
            padding: 30px 20px;
        }

        .register-title {
            font-size: 24px;
        }
    }
</style>
</head>
<body>

<div class="register-container">
    <div class="register-card">

        <h3 class="register-title">Create Your Account</h3>

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
                    <input type="password" name="password" class="form-control"required>
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

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>

    </div>
</div>

</body>
</html>
