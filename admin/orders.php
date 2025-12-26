<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

/* UPDATE ORDER STATUS */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->execute([$status, $order_id]);
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

<h2>Manage Orders</h2>

<table border="1" cellpadding="8">
    <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Cell Phone</th>
        <th>Order Date</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th>Items</th>
        <th>Action</th>
    </tr>

    <?php foreach($orders as $o): ?>
    <tr>
        <td><?= $o['order_id']; ?></td>
        <td><?= htmlspecialchars($o['name']); ?></td>
        <td><?= htmlspecialchars($o['email']); ?></td>
        <td><?= htmlspecialchars($o['cell_phone']); ?></td>
        <td><?= $o['order_date']; ?></td>
        <td><?= number_format($o['total_amount'], 2); ?></td>
        <td><?= $o['status']; ?></td>
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
                <?php foreach($items as $item): ?>
                    <li>
                        <?= htmlspecialchars($item['product_name']); ?> -
                        <?= $item['quantity']; ?> x <?= number_format($item['price'], 2); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </td>
        <td>
            <form method="post">
                <input type="hidden" name="order_id" value="<?= $o['order_id']; ?>">
                <select name="status">
                    <option value="Pending" <?= $o['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Confirmed" <?= $o['status'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="Delivered" <?= $o['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="Cancelled" <?= $o['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" name="update_status">Update</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
