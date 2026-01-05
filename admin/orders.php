<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

/* UPDATE ORDER STATUS */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status   = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$status, $order_id]);

    header("Location: orders.php");
    exit;
}

/* FETCH ORDERS WITH CUSTOMER INFO */
$stmt = $conn->query("
    SELECT o.*, c.name, c.email, c.cell_phone
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Orders | Admin</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Font (same as other admin page for navbar consistency) -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f7f5;
    color: #063c2e;
}

/* Heading */
h2 {
    font-weight: 700;
    font-size: 28px;
    margin-bottom: 30px;
    background: linear-gradient(90deg, #0b6b4f, #158f6b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Table Container */
.table-container {
    background: #ffffff;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.08);
}

/* Table Styling */
table {
    border-collapse: separate;
    border-spacing: 0;
}

table thead th {
    background-color: #d7efe6;
    color: #0b6b4f;
    font-weight: 600;
    text-align: center;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

table tbody tr {
    transition: 0.25s;
}

table tbody tr:nth-child(even) {
    background: #f9fcfb;
}

table tbody tr:hover {
    background: #e0f1ea;
    transform: scale(1.01);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

table td {
    vertical-align: middle;
    padding: 12px 10px;
}

table img {
    border-radius: 8px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Status Badges */
.status-badge {
    padding: 5px 14px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
}

.Pending { background: linear-gradient(135deg,#ffc107,#ffea80); color:#063c2e; }
.Confirmed { background: linear-gradient(135deg,#0dcaf0,#a0e5ff); color:#063c2e; }
.Delivered { background: linear-gradient(135deg,#198754,#5cc28f); color:#fff; }
.Cancelled { background: linear-gradient(135deg,#dc3545,#ff7a7a); color:#fff; }

/* Form Controls */
select, button {
    border-radius: 8px;
    font-size: 14px;
    border: 1px solid #0b6b4f;
}

select {
    padding: 6px 10px;
    background: #f9fcfb;
    color: #063c2e;
    font-weight: 500;
}

button {
    padding: 6px 16px;
    font-weight: 600;
    background: linear-gradient(135deg,#0b6b4f,#158f6b);
    color: #fff;
    transition: 0.25s;
}

button:hover {
    background: linear-gradient(135deg, #16a085, #1abc9c);
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(22,160,133,0.35);
}

/* Item List */
ul {
    padding-left: 18px;
    margin-bottom: 0;
}

/* Responsive */
@media(max-width: 768px) {
    h2 { font-size: 24px; }
    table th, table td { font-size: 13px; }
}
</style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="container mt-5 mb-5">
    <h2>Manage Orders</h2>

    <div class="table-container table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Order Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= $o['order_id']; ?></td>
                    <td>
                        <?= htmlspecialchars($o['name']); ?><br>
                        <small><?= htmlspecialchars($o['email']); ?></small>
                    </td>
                    <td><?= htmlspecialchars($o['cell_phone']); ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($o['order_date'])); ?></td>
                    <td>₨ <?= number_format($o['total_amount'], 2); ?></td>
                    <td>
                        <span class="status-badge <?= $o['status']; ?>">
                            <?= $o['status']; ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $stmtItems = $conn->prepare("
                            SELECT p.product_name, oi.quantity, oi.price
                            FROM order_items oi
                            JOIN products p ON oi.product_id = p.product_id
                            WHERE oi.order_id = ?
                        ");
                        $stmtItems->execute([$o['order_id']]);
                        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <ul>
                        <?php foreach ($items as $item): ?>
                            <li><?= htmlspecialchars($item['product_name']); ?> (<?= $item['quantity']; ?> × ₨<?= number_format($item['price'],2); ?>)</li>
                        <?php endforeach; ?>
                        </ul>
                    </td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="order_id" value="<?= $o['order_id']; ?>">
                            <select name="status">
                                <?php foreach (['Pending','Confirmed','Delivered','Cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
