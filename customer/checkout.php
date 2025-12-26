<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

$customer_id = $_SESSION['customer_id'];

/* GET CUSTOMER DETAILS */
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

/* GET CART */
$stmt = $conn->prepare("SELECT * FROM cart WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

$cart_id = $cart['cart_id'] ?? null;

if (!$cart_id) {
    die("Your cart is empty!");
}

/* FETCH CART ITEMS */
$stmt = $conn->prepare("
    SELECT ci.cart_item_id, ci.product_id, ci.quantity, p.price
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.product_id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$items) {
    die("Your cart is empty!");
}

/* PLACE ORDER */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {

    // Calculate total amount
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Insert into orders table
    $stmt = $conn->prepare("
        INSERT INTO orders (customer_id, total_amount)
        VALUES (?, ?)
    ");
    $stmt->execute([$customer_id, $total]);
    $order_id = $conn->lastInsertId();

    // Insert into order_items
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($items as $item) {
        $stmt->execute([
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        ]);
    }

    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
    $stmt->execute([$cart_id]);

    // Redirect to order success page
    header("Location: orders.php?success=1");
    exit;
}
?>

<h2>Checkout</h2>

<p><strong>Name:</strong> <?= htmlspecialchars($customer['name']); ?></p>
<p><strong>Address:</strong> <?= htmlspecialchars($customer['address']); ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($customer['email']); ?></p>
<p><strong>Cell Phone:</strong> <?= htmlspecialchars($customer['cell_phone']); ?></p>

<table border="1" cellpadding="8">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
    </tr>
    <?php $grandTotal = 0; ?>
    <?php foreach ($items as $item): ?>
        <?php $total = $item['price'] * $item['quantity']; $grandTotal += $total; ?>
        <tr>
            <td><?= htmlspecialchars($item['product_id']); ?></td>
            <td><?= $item['price']; ?></td>
            <td><?= $item['quantity']; ?></td>
            <td><?= number_format($total, 2); ?></td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3"><strong>Grand Total</strong></td>
        <td><strong><?= number_format($grandTotal, 2); ?></strong></td>
    </tr>
</table>

<form method="post">
    <button type="submit" name="place_order">Place Order</button>
</form>
