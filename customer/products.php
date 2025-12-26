<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

// FETCH CATEGORIES
$catStmt = $conn->query("SELECT * FROM categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// SEARCH & FILTER LOGIC
$where = [];
$params = [];

if (!empty($_GET['search'])) {
    $where[] = "p.product_name LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

if (!empty($_GET['category_id'])) {
    $where[] = "p.category_id = ?";
    $params[] = $_GET['category_id'];
}

$sql = "
    SELECT p.*, c.category_name
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    WHERE p.status = 'Available'
";

if ($where) {
    $sql .= " AND " . implode(" AND ", $where);
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Products</h2>

<form method="get">
    <input type="text" name="search" placeholder="Search product name"
           value="<?= $_GET['search'] ?? '' ?>">

    <select name="category_id">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['category_id']; ?>"
                <?= (($_GET['category_id'] ?? '') == $cat['category_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['category_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Search</button>
</form>

<hr>

<table border="1" cellpadding="8">
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Action</th>
    </tr>

    <?php foreach ($products as $p): ?>
    <tr>
        <td>
            <?php if ($p['image_url']): ?>
                <img src="../uploads/<?= $p['image_url']; ?>" width="80">
            <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($p['product_name']); ?></td>
        <td><?= htmlspecialchars($p['category_name']); ?></td>
        <td><?= $p['price']; ?></td>
        <td><?= $p['stock_quantity']; ?></td>
        <td>
            <a href="cart.php?add=<?= $p['product_id']; ?>">
                Add to Cart
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
