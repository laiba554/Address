<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Trim inputs
    $name       = trim($_POST['name'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $work_phone = trim($_POST['work_phone'] ?? '');
    $cell_phone = trim($_POST['cell_phone'] ?? '');
    $dob        = $_POST['date_of_birth'] ?? '';
    $remarks    = trim($_POST['remarks'] ?? '');
    $password_raw = $_POST['password'] ?? '';

    // Normalize phone numbers (digits only)
    $cell_digits = preg_replace('/\D/', '', $cell_phone);
    $work_digits = preg_replace('/\D/', '', $work_phone);

    /* =======================
       SERVER-SIDE VALIDATIONS
    ======================== */

    if ($name === '' || strlen($name) < 3 || strlen($name) > 100 || !preg_match("/^[a-zA-Z\s.'-]+$/", $name)) {
        $error = "Please enter a valid full name.";
    }
    elseif ($address === '' || strlen($address) < 5 || strlen($address) > 255) {
        $error = "Please enter a valid address.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }
    elseif ($cell_digits === '' || strlen($cell_digits) !== 11) {
        $error = "Cell phone number must contain exactly 11 digits.";
    }
    elseif ($work_phone !== '' && strlen($work_digits) !== 11) {
        $error = "Work phone number must contain exactly 11 digits.";
    }
    elseif ($dob === '' || strtotime($dob) === false || strtotime($dob) > time()) {
        $error = "Please enter a valid date of birth.";
    }
    else {
        // AGE VALIDATION (12+)
        $dobDate = new DateTime($dob);
        $today   = new DateTime();
        $age     = $today->diff($dobDate)->y;

        if ($age < 12) {
            $error = "You must be at least 12 years old to create an account.";
        }
        elseif ($password_raw === '' || strlen($password_raw) < 8) {
            $error = "Password must be at least 8 characters long.";
        }
        else {

            // Hash password after validation
            $password = password_hash($password_raw, PASSWORD_DEFAULT);

            try {

                // Check duplicate email
                $checkStmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
                $checkStmt->execute([$email]);

                if ($checkStmt->rowCount() > 0) {
                    $error = "Email already exists.";
                } else {

                    $stmt = $conn->prepare("
                        INSERT INTO customers
                        (name, address, email, work_phone, cell_phone, date_of_birth, remarks, password)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");

                    $stmt->execute([
                        $name,
                        $address,
                        $email,
                        $work_phone,
                        $cell_phone,
                        $dob,
                        $remarks,
                        $password
                    ]);

                    $_SESSION['customer_id'] = $conn->lastInsertId();
                    $_SESSION['customer_name'] = $name;

                    header("Location: products.php");
                    exit;
                }

            } catch (PDOException $e) {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Registration | Address Jewelers</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f0f5f3, #e0ece7);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.register-container { max-width: 700px; width: 100%; margin-bottom: 60px; }
.register-card {
    background: #fff;
    padding: 40px 35px;
    border-radius: 20px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
}
.register-title {
    font-weight: 700;
    color: #0f3d2e;
    font-size: 28px;
    text-align: center;
    margin-bottom: 30px;
}
.form-label { font-weight: 500; color: #0f3d2e; }
.form-control, textarea {
    border-radius: 12px;
    border: 1px solid #cdd9d6;
    padding: 10px 12px;
    font-size: 14px;
}
.btn-register {
    background: linear-gradient(135deg, #1abc9c, #16a085);
    color: #fff;
    font-weight: 600;
    border-radius: 30px;
    padding: 12px 0;
    border: none;
}
.error-msg {
    background: #ffecec;
    color: #d8000c;
    padding: 12px;
    border-radius: 12px;
    margin-bottom: 20px;
    text-align: center;
}
footer {
    background: #0b6b4f;
    color: #f0f0f0;
    text-align: center;
    padding: 20px 0;
    font-size: 14px;
    position: fixed;
    bottom: 0;
    width: 100%;
}
</style>
</head>
<body>

<div class="register-container">
<div class="register-card">

<h3 class="register-title">Create Your Account</h3>

<?php if ($error): ?>
<div class="error-msg"><?= htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post">
<div class="row g-3">

<div class="col-md-12">
<label class="form-label">Full Name</label>
<input type="text" name="name" class="form-control"
required minlength="3" maxlength="100"
pattern="[A-Za-z\s.'-]+">
</div>

<div class="col-md-12">
<label class="form-label">Address</label>
<textarea name="address" class="form-control"
required minlength="5" maxlength="255"></textarea>
</div>

<div class="col-md-12">
<label class="form-label">Email Address</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="col-md-6">
<label class="form-label">Work Phone</label>
<input type="text" name="work_phone" class="form-control"
pattern="\d{11}" maxlength="11" inputmode="numeric">
</div>

<div class="col-md-6">
<label class="form-label">Cell Phone</label>
<input type="text" name="cell_phone" class="form-control"
required pattern="\d{11}" maxlength="11" inputmode="numeric">
</div>

<div class="col-md-6">
<label class="form-label">Date of Birth</label>
<input type="date" name="date_of_birth" class="form-control" required>
</div>

<div class="col-md-6">
<label class="form-label">Password</label>
<input type="password" name="password" class="form-control"
required minlength="8">
</div>

<div class="col-md-12">
<label class="form-label">Remarks (Optional)</label>
<textarea name="remarks" class="form-control" maxlength="500"></textarea>
</div>

<div class="col-12 d-grid mt-3">
<button type="submit" class="btn btn-register">Register Account</button>
</div>

</div>
</form>

<div class="login-link">
Already have an account? <a href="login.php">Login here</a>
</div>

</div>
</div>

<footer>
© <?= date('Y'); ?> Jenny Store. All Rights Reserved.
</footer>

</body>
</html>
