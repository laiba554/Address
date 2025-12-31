<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

$success = "";
$error = "";

/* ADD CATEGORY */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_category'])) {

    $category_name = trim($_POST['category_name']);
    $description   = trim($_POST['description']);

    if (empty($category_name)) {
        $error = "Category name is required.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO categories (category_name, description) VALUES (?, ?)"
        );
        $stmt->execute([$category_name, $description]);

        $success = "Category added successfully.";
    }
}

/* DELETE CATEGORY */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $stmt->execute([$id]);

    $success = "Category deleted successfully.";
}

/* FETCH CATEGORIES */
$stmt = $conn->query("SELECT * FROM categories ORDER BY created_at DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories | Admin Panel</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Internal CSS Theme -->
    <style>
        body {
            background-color: #1E1E2F;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .admin-box {
            background-color: #2C2C3E;
            padding: 30px;
            margin-top: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }

        h2, h4 {
            color: #FFD700;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        .form-control, textarea {
            background-color: #1E1E2F;
            color: #fff;
            border: 1px solid #555;
        }

        .form-control:focus {
            background-color: #1E1E2F;
            color: #fff;
            border-color: #FFD700;
            box-shadow: none;
        }

        .btn-gold {
            background-color: #FFD700;
            color: #1E1E2F;
            font-weight: bold;
            border: none;
        }

        .btn-gold:hover {
            background-color: #e6c200;
        }

        table {
            background-color: #1E1E2F;
        }

        table th {
            background-color: #111;
            color: #FFD700;
            text-align: center;
        }

        table td {
            text-align: center;
            vertical-align: middle;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: #fff;
            border: none;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .alert-success {
            background-color: #2ecc71;
            color: #000;
            border: none;
        }

        .alert-danger {
            background-color: #e74c3c;
            color: #fff;
            border: none;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<?php include "../includes/navbar.php"; ?>

<div class="container admin-box">

    <h2 class="text-center mb-4">Manage Categories</h2>

    <!-- Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Add Category Form -->
    <h4>Add New Category</h4>
    <form method="post" class="mb-4">
        <div class="mb-3">
            <label>Category Name</label>
            <input type="text" name="category_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" name="add_category" class="btn btn-gold px-4">
            Add Category
        </button>
    </form>

    <hr style="border-color:#444;">

    <!-- Category List -->
    <h4 class="mb-3">Category List</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($categories) > 0): ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= (int)$cat['category_id']; ?></td>
                            <td><?= htmlspecialchars($cat['category_name']); ?></td>
                            <td><?= htmlspecialchars($cat['description']); ?></td>
                            <td>
                                <a href="?delete=<?= (int)$cat['category_id']; ?>"
                                   class="btn btn-delete btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this category?')">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No categories found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Footer -->
<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
