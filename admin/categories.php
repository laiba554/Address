<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

// ADD CATEGORY
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $description = trim($_POST['description']);

    if (!empty($category_name)) {
        $stmt = $conn->prepare(
            "INSERT INTO categories (category_name, description) VALUES (?, ?)"
        );
        $stmt->execute([$category_name, $description]);
    }
}

// DELETE CATEGORY
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->execute([$id]);
}

// FETCH CATEGORIES
$stmt = $conn->query("SELECT * FROM categories ORDER BY created_at DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Categories</h2>

<form method="post">
    <label>Category Name:</label><br>
    <input type="text" name="category_name" required><br><br>

    <label>Description:</label><br>
    <textarea name="description"></textarea><br><br>

    <button type="submit" name="add_category">Add Category</button>
</form>

<hr>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Category Name</th>
        <th>Description</th>
        <th>Action</th>
    </tr>

    <?php foreach ($categories as $cat): ?>
    <tr>
        <td><?= $cat['category_id']; ?></td>
        <td><?= htmlspecialchars($cat['category_name']); ?></td>
        <td><?= htmlspecialchars($cat['description']); ?></td>
        <td>
            <a href="?delete=<?= $cat['category_id']; ?>"
               onclick="return confirm('Delete this category?')">
               Delete
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
