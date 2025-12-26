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

        // Auto login after registration
        $_SESSION['customer_id'] = $conn->lastInsertId();
        $_SESSION['customer_name'] = $name;
        header("Location: products.php");
        exit;

    } catch (PDOException $e) {
        $error = "Email already exists";
    }
}
?>

<h2>Customer Registration</h2>

<form method="post">
    <input type="text" name="name" placeholder="Full Name" required><br><br>
    <textarea name="address" placeholder="Address" required></textarea><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="text" name="work_phone" placeholder="Work Phone"><br><br>
    <input type="text" name="cell_phone" placeholder="Cell Phone" required><br><br>
    <input type="date" name="date_of_birth" required><br><br>
    <textarea name="remarks" placeholder="Remarks"></textarea><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>

    <button type="submit">Register</button>
</form>

<p style="color:red"><?= $error; ?></p>
