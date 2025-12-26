<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

$customer_id = $_SESSION['customer_id'];

/* -------------------------------------------------
   GET or CREATE CART
------------------------------------------------- */
$stmt = $conn->prepare("SELECT * FROM cart WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    $stmt = $conn->prepare("INSERT INTO cart (customer_id) VALUES (?)");
    $stmt->execute([$customer_id]);
    $cart_id = $conn->lastInsertId();
} else {
    $cart_id = $cart['cart_id'];
}

/* -------------------------------------------------
   ADD PRODUCT TO CART
------------------------------------------------- */
if (isset($_GET['add'])) {
    $product_id = intval($_GET['add']);

    $stmt = $conn->prepare("
        SELECT * FROM cart_items
        WHERE cart_id = ? AND product_id = ?
    ");
    $stmt->execute([$cart_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $stmt = $conn->prepare("
            UPDATE cart_items SET quantity = quantity + 1
            WHERE cart_item_id = ?
        ");
        $stmt->execute([$item['cart_item_id']]);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO cart_items (cart_id, product_id, quantity)
            VALUES (?, ?, 1)
        ");
        $stmt->execute([$cart_id, $product_id]);
    }

    header("Location: cart.php");
    exit;
}

/* -------------------------------------------------
   UPDATE QUANTITY
------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $item_id => $qty) {
        $stmt = $conn->prepare("
            UPDATE cart_items SET quantity = ?
            WHERE cart_item_id = ?
        ");
        $stmt->execute([$qty, $item_id]);
    }
}

/* -------------------------------------------------
   REMOVE ITEM
------------------------------------------------- */
if (isset($_GET['remove'])) {
    $stmt = $conn->prepare("
        DELETE FROM cart_items WHERE cart_item_id = ?
    ");
    $stmt->execute([intval($_GET['remove'])]);
}

/* -------------------------------------------------
   FETCH CART ITEMS
------------------------------------------------- */
$stmt = $conn->prepare("
    SELECT ci.cart_item_id, p.product_name, p.price, ci.quantity
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.product_id
    WHERE ci.cart_id = ?
");
$stmt->execute([$cart_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Your Shopping Cart</h2>

<form method="post">
<table border="1" cellpadding="8">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Action</th>
    </tr>

    <?php $grandTotal = 0; ?>
    <?php foreach ($items as $item): ?>
        <?php
            $total = $item['price'] * $item['quantity'];
            $grandTotal += $total;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']); ?></td>
            <td><?= $item['price']; ?></td>
            <td>
                <input type="number"
                       name="quantity[<?= $item['cart_item_id']; ?>]"
                       value="<?= $item['quantity']; ?>"
                       min="1">
            </td>
            <td><?= number_format($total, 2); ?></td>
            <td>
                <a href="?remove=<?= $item['cart_item_id']; ?>">Remove</a>
            </td>
        </tr>
    <?php endforeach; ?>

    <tr>
        <td colspan="3"><strong>Grand Total</strong></td>
        <td colspan="2"><strong><?= number_format($grandTotal, 2); ?></strong></td>
    </tr>
</table>

<br>
<button type="submit" name="update">Update Cart</button>
<a href="checkout.php">Proceed to Checkout</a>
</form>
