<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

/* FETCH CATEGORIES */
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name")
                   ->fetchAll(PDO::FETCH_ASSOC);

/* ADD PRODUCT */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {

    $category_id = intval($_POST['category_id']);
    $name        = trim($_POST['product_name']);
    $desc        = trim($_POST['product_description']);
    $price       = floatval($_POST['price']);
    $stock       = intval($_POST['stock_quantity']);
    $status      = $_POST['status'];

    /* IMAGE UPLOAD */
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $imageName);
    }

    $stmt = $conn->prepare("
        INSERT INTO products 
        (category_id, product_name, product_description, price, stock_quantity, image_url, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$category_id, $name, $desc, $price, $stock, $imageName, $status]);

    header("Location: products.php");
    exit;
}

/* DELETE PRODUCT */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    /* Remove image */
    $img = $conn->prepare("SELECT image_url FROM products WHERE product_id=?");
    $img->execute([$id]);
    if ($row = $img->fetch()) {
        if ($row['image_url'] && file_exists("../uploads/".$row['image_url'])) {
            unlink("../uploads/".$row['image_url']);
        }
    }

    $conn->prepare("DELETE FROM products WHERE product_id=?")->execute([$id]);
    header("Location: products.php");
    exit;
}

/* FETCH PRODUCTS */
$products = $conn->query("
    SELECT p.*, c.category_name 
    FROM products p
    JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#1E1E2F; color:#fff; }
        h2 { color:#FFD700; }

        .box {
            background:#2C2C3E;
            padding:25px;
            border-radius:12px;
            box-shadow:0 8px 20px rgba(0,0,0,.4);
        }

        label { color:#FFD700; }

        input, textarea, select {
            background:#1E1E2F!important;
            color:#fff!important;
            border:1px solid #FFD700!important;
        }

        button {
            background:#FFD700;
            color:#000;
            font-weight:bold;
            border:none;
        }

        table th { color:#FFD700; background:#3a3a55; }
        table img { border-radius:8px; }
    </style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="container mt-5 mb-5">
    <h2>Manage Products</h2>

    <!-- ADD PRODUCT FORM -->
    <div class="box mb-5">
        <form method="post" enctype="multipart/form-data">
            <div class="row g-3">

                <div class="col-md-4">
                    <label>Category</label>
                    <select name="category_id" class="form-control" required>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['category_id'] ?>">
                                <?= htmlspecialchars($c['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Product Name</label>
                    <input type="text" name="product_name" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option>Available</option>
                        <option>Out of Stock</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Product Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="col-12">
                    <label>Description</label>
                    <textarea name="product_description" class="form-control"></textarea>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" name="add_product" class="btn px-4">Add Product</button>
                </div>
            </div>
        </form>
    </div>

    <!-- PRODUCT LIST -->
    <div class="box table-responsive">
        <table class="table table-dark table-bordered align-middle">
            <thead>
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
            </thead>
            <tbody>
            <?php foreach($products as $p): ?>
                <tr>
                    <td><?= $p['product_id'] ?></td>
                    <td>
                        <?php if($p['image_url']): ?>
                            <img src="../uploads/<?= $p['image_url'] ?>" width="70">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['product_name']) ?></td>
                    <td><?= htmlspecialchars($p['category_name']) ?></td>
                    <td>₹ <?= number_format($p['price'],2) ?></td>
                    <td><?= $p['stock_quantity'] ?></td>
                    <td><?= $p['status'] ?></td>
                    <td>
                        <a href="?delete=<?= $p['product_id'] ?>"
                           onclick="return confirm('Delete this product?')"
                           class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
