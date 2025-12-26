<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

// FETCH CATEGORIES (for dropdown)
$catStmt = $conn->query("SELECT * FROM categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// ADD PRODUCT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {
    $category_id = $_POST['category_id'];
    $name = trim($_POST['product_name']);
    $description = trim($_POST['product_description']);
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];
    $status = $_POST['status'];

    // Image upload
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../uploads/" . $imageName
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO products
        (category_id, product_name, product_description, price, stock_quantity, image_url, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $category_id,
        $name,
        $description,
        $price,
        $stock,
        $imageName,
        $status
    ]);
}

// DELETE PRODUCT
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
}

// FETCH PRODUCTS
$stmt = $conn->query("
    SELECT p.*, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Products</h2>

<form method="post" enctype="multipart/form-data">
    <label>Category:</label><br>
    <select name="category_id" required>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['category_id']; ?>">
                <?= htmlspecialchars($cat['category_name']); ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Product Name:</label><br>
    <input type="text" name="product_name" required><br><br>

    <label>Description:</label><br>
    <textarea name="product_description"></textarea><br><br>

    <label>Price:</label><br>
    <input type="number" step="0.01" name="price" required><br><br>

    <label>Stock Quantity:</label><br>
    <input type="number" name="stock_quantity" required><br><br>

    <label>Status:</label><br>
    <select name="status">
        <option value="Available">Available</option>
        <option value="Out of Stock">Out of Stock</option>
    </select><br><br>

    <label>Product Image:</label><br>
    <input type="file" name="image"><br><br>

    <button type="submit" name="add_product">Add Product</button>
</form>

<hr>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php foreach ($products as $p): ?>
    <tr>
        <td><?= $p['product_id']; ?></td>
        <td>
            <?php if ($p['image_url']): ?>
                <img src="../uploads/<?= $p['image_url']; ?>" width="60">
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($p['product_name']); ?></td>
        <td><?= htmlspecialchars($p['category_name']); ?></td>
        <td><?= $p['price']; ?></td>
        <td><?= $p['stock_quantity']; ?></td>
        <td><?= $p['status']; ?></td>
        <td>
            <a href="?delete=<?= $p['product_id']; ?>"
               onclick="return confirm('Delete product?')">
               Delete
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
