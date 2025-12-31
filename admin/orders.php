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

    <!-- Internal CSS -->
    <style>
        body {
            background-color: #1E1E2F;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        h2 {
            color: #FFD700;
            margin-bottom: 25px;
        }

        .table-container {
            background-color: #2C2C3E;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        table th {
            background-color: #3a3a55;
            color: #FFD700;
            white-space: nowrap;
        }

        table td {
            vertical-align: top;
        }

        select, button {
            background-color: #1E1E2F;
            color: #FFD700;
            border: 1px solid #FFD700;
            border-radius: 6px;
            padding: 6px;
        }

        button:hover {
            background-color: #FFD700;
            color: #1E1E2F;
        }

        ul {
            padding-left: 18px;
            margin-bottom: 0;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 13px;
        }

        .Pending { background-color: orange; color: #000; }
        .Confirmed { background-color: #0dcaf0; color: #000; }
        .Delivered { background-color: #198754; }
        .Cancelled { background-color: #dc3545; }
    </style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="container mt-5 mb-5">
    <h2>Manage Orders</h2>

    <div class="table-container table-responsive">
        <table class="table table-bordered table-dark table-hover align-middle">
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
                    <td>₹ <?= number_format($o['total_amount'], 2); ?></td>
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
                                <li>
                                    <?= htmlspecialchars($item['product_name']); ?>
                                    (<?= $item['quantity']; ?> × ₹<?= number_format($item['price'], 2); ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="order_id" value="<?= $o['order_id']; ?>">
                            <select name="status">
                                <?php foreach (['Pending','Confirmed','Delivered','Cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $o['status']===$s?'selected':'' ?>>
                                        <?= $s ?>
                                    </option>
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
