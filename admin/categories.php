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

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f7f5;
        color: #2c2c2c;
    }

    .admin-box {
        max-width: 1150px;
        background: #ffffff;
        padding: 40px;
        margin: 40px auto;
        border-radius: 18px;
        box-shadow: 0 28px 60px rgba(0,0,0,0.08);
        border: 1px solid #e0ebe5;
    }

    .page-title {
        text-align: center;
        font-weight: 700;
        font-size: 30px;
        margin-bottom: 30px;
        background: linear-gradient(90deg, #0f3d2e, #2f6f5c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .section-title {
        font-weight: 600;
        font-size: 20px;
        color: #0f3d2e;
        margin-bottom: 15px;
    }

    label {
        font-weight: 500;
        margin-bottom: 6px;
    }

    .form-control {
        border-radius: 12px;
        border: 1px solid #d6e5df;
        padding: 12px 14px;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: #1abc9c;
        box-shadow: 0 0 0 0.15rem rgba(26,188,156,.25);
    }

    .btn-primary-custom {
         background: linear-gradient(135deg, #0b6b4f, #158f6b);
            color: #ffffff;
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 30px;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
        background: linear-gradient(135deg, #16a085, #1abc9c);
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(22,160,133,0.35);
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-radius: 12px;
        border: none;
        font-weight: 500;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-radius: 12px;
        border: none;
        font-weight: 500;
    }

    .table-wrapper {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 18px 40px rgba(0,0,0,0.06);
        overflow: hidden;
        border: 1px solid #e0ebe5;
    }

    table {
        margin-bottom: 0;
    }

    table thead {
        background: #f1f7f6;
    }

    table th {
        font-weight: 600;
        color: #0f3d2e;
        text-align: center;
        padding: 14px;
        font-size: 14px;
    }

    table td {
        text-align: center;
        vertical-align: middle;
        padding: 14px;
        font-size: 14px;
    }

    table tbody tr:hover {
        background: #f7fbfa;
    }

    .btn-delete {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        box-shadow: 0 8px 20px rgba(231,76,60,0.35);
        transform: translateY(-1px);
    }

    hr {
        border-color: #e0ebe5;
        margin: 30px 0;
    }

    @media (max-width: 768px) {
        .admin-box {
            padding: 25px 20px;
        }

        .page-title {
            font-size: 26px;
        }
    }
</style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="admin-box">

    <h2 class="page-title">Manage Categories</h2>

    <?php if ($success): ?>
        <div class="alert alert-success text-center mb-4">
            <?= htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center mb-4">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <h4 class="section-title">Add New Category</h4>

    <form method="post" class="mb-4">
        <div class="mb-3">
            <label>Category Name</label>
            <input type="text" name="category_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" name="add_category" class="btn-primary-custom">
            Add Category
        </button>
    </form>

    <hr>

    <h4 class="section-title">Category List</h4>

    <div class="table-wrapper table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($categories): ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= (int)$cat['category_id']; ?></td>
                            <td><?= htmlspecialchars($cat['category_name']); ?></td>
                            <td><?= htmlspecialchars($cat['description']); ?></td>
                            <td>
                                <a href="?delete=<?= (int)$cat['category_id']; ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this category?')">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-muted">No categories found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
