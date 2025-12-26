<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

$customer_id = $_SESSION['customer_id'];

/* FETCH CUSTOMER ORDERS */
$stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE customer_id = ?
    ORDER BY order_date DESC
");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Orders</h2>

<?php if (empty($orders)): ?>
    <p>You have not placed any orders yet.</p>
<?php else: ?>
<table border="1" cellpadding="8">
    <tr>
        <th>Order ID</th>
        <th>Order Date</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th>Items</th>
    </tr>

    <?php foreach($orders as $o): ?>
    <tr>
        <td><?= $o['order_id']; ?></td>
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
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
