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

    $stmt = $conn->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?");
    $stmt->execute([$cart_id, $product_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE cart_item_id = ?");
        $stmt->execute([$item['cart_item_id']]);
    } else {
        $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, 1)");
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
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
        $stmt->execute([$qty, $item_id]);
    }
}

/* -------------------------------------------------
   REMOVE ITEM
------------------------------------------------- */
if (isset($_GET['remove'])) {
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_item_id = ?");
    $stmt->execute([intval($_GET['remove'])]);
    header("Location: cart.php");
    exit;
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart | Address Jewelers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f4f9;
            padding-top: 60px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        table {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th {
            background-color: #007bff;
            color: #fff;
            text-align: center;
        }

        td {
            vertical-align: middle;
            text-align: center;
        }

        input[type=number] {
            width: 70px;
            text-align: center;
        }

        .btn-update {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }

        .btn-update:hover {
            background-color: #218838;
            color: white;
        }

        .btn-remove {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
        }

        .btn-remove:hover {
            background-color: #c82333;
            color: white;
        }

        .btn-checkout {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .btn-checkout:hover {
            background-color: #0056b3;
            color: white;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
            padding-right: 20px;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            td, th {
                font-size: 14px;
            }

            input[type=number] {
                width: 50px;
            }

            .cart-actions {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Shopping Cart</h2>

    <form method="post">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price ($)</th>
                        <th>Quantity</th>
                        <th>Total ($)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grandTotal = 0; ?>
                    <?php foreach ($items as $item): ?>
                        <?php
                            $total = $item['price'] * $item['quantity'];
                            $grandTotal += $total;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']); ?></td>
                            <td><?= number_format($item['price'], 2); ?></td>
                            <td>
                                <input type="number"
                                       name="quantity[<?= $item['cart_item_id']; ?>]"
                                       value="<?= $item['quantity']; ?>"
                                       min="1">
                            </td>
                            <td><?= number_format($total, 2); ?></td>
                            <td>
                                <a href="?remove=<?= $item['cart_item_id']; ?>" class="btn btn-remove btn-sm">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">Your cart is empty.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="grand-total mb-3">
            Grand Total: $<?= number_format($grandTotal, 2); ?>
        </div>

        <div class="cart-actions">
            <button type="submit" name="update" class="btn btn-update">Update Cart</button>
            <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
