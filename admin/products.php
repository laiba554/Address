<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

/* FETCH CATEGORIES */
$categories = $conn->query("
    SELECT * FROM categories ORDER BY category_name
")->fetchAll(PDO::FETCH_ASSOC);

/* ===========================
   EDIT MODE FETCH
=========================== */
$editProduct = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ===========================
   ADD PRODUCT
=========================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {

    $category_id = $_POST['category_id'];
    $name = trim($_POST['product_name']);
    $desc = trim($_POST['product_description']);
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];
    $status = $_POST['status'];

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
    $stmt->execute([$category_id,$name,$desc,$price,$stock,$imageName,$status]);

    header("Location: products.php");
    exit;
}

/* ===========================
   UPDATE PRODUCT
=========================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_product'])) {

    $id = $_POST['product_id'];
    $category_id = $_POST['category_id'];
    $name = trim($_POST['product_name']);
    $desc = trim($_POST['product_description']);
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];
    $status = $_POST['status'];
    $oldImage = $_POST['old_image'];

    $imageName = $oldImage;

    if (!empty($_FILES['image']['name'])) {
        if ($oldImage && file_exists("../uploads/".$oldImage)) {
            unlink("../uploads/".$oldImage);
        }
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = time()."_".uniqid().".".$ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/".$imageName);
    }

    $stmt = $conn->prepare("
        UPDATE products SET
            category_id=?,
            product_name=?,
            product_description=?,
            price=?,
            stock_quantity=?,
            image_url=?,
            status=?
        WHERE product_id=?
    ");
    $stmt->execute([
        $category_id,$name,$desc,$price,$stock,$imageName,$status,$id
    ]);

    header("Location: products.php");
    exit;
}

/* ===========================
   DELETE PRODUCT
=========================== */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $img = $conn->prepare("SELECT image_url FROM products WHERE product_id=?");
    $img->execute([$id]);
    $p = $img->fetch(PDO::FETCH_ASSOC);

    $conn->prepare("DELETE FROM cart_items WHERE product_id=?")->execute([$id]);
    $conn->prepare("DELETE FROM products WHERE product_id=?")->execute([$id]);

    if ($p && $p['image_url'] && file_exists("../uploads/".$p['image_url'])) {
        unlink("../uploads/".$p['image_url']);
    }

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
body{background:#1E1E2F;color:#fff}
h2{color:#FFD700}
.box{background:#2C2C3E;padding:25px;border-radius:12px}
label{color:#FFD700}
input,textarea,select{
background:#1E1E2F!important;
color:#fff!important;
border:1px solid #FFD700!important
}
button{background:#FFD700;color:#000;font-weight:bold}
table th{color:#FFD700;background:#3a3a55}
</style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="container mt-5 mb-5">
<h2><?= $editProduct ? 'Update Product' : 'Add Product' ?></h2>

<div class="box mb-5">
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="product_id" value="<?= $editProduct['product_id'] ?? '' ?>">
<input type="hidden" name="old_image" value="<?= $editProduct['image_url'] ?? '' ?>">

<div class="row g-3">
<div class="col-md-4">
<label>Category</label>
<select name="category_id" class="form-control">
<?php foreach($categories as $c): ?>
<option value="<?= $c['category_id'] ?>"
<?= ($editProduct && $editProduct['category_id']==$c['category_id'])?'selected':'' ?>>
<?= $c['category_name'] ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-4">
<label>Name</label>
<input type="text" name="product_name" class="form-control"
value="<?= $editProduct['product_name'] ?? '' ?>" required>
</div>

<div class="col-md-4">
<label>Price</label>
<input type="number" step="0.01" name="price" class="form-control"
value="<?= $editProduct['price'] ?? '' ?>" required>
</div>

<div class="col-md-4">
<label>Stock</label>
<input type="number" name="stock_quantity" class="form-control"
value="<?= $editProduct['stock_quantity'] ?? '' ?>" required>
</div>

<div class="col-md-4">
<label>Status</label>
<select name="status" class="form-control">
<option <?= ($editProduct['status']??'')=='Available'?'selected':'' ?>>Available</option>
<option <?= ($editProduct['status']??'')=='Out of Stock'?'selected':'' ?>>Out of Stock</option>
</select>
</div>

<div class="col-md-4">
<label>Image</label>
<input type="file" name="image" class="form-control">
</div>

<div class="col-12">
<label>Description</label>
<textarea name="product_description" class="form-control"><?= $editProduct['product_description'] ?? '' ?></textarea>
</div>

<div class="col-12 text-end">
<button type="submit" name="<?= $editProduct ? 'update_product' : 'add_product' ?>">
<?= $editProduct ? 'Update Product' : 'Add Product' ?>
</button>
</div>
</div>
</form>
</div>

<div class="box table-responsive">
<table class="table table-dark table-bordered">
<thead>
<tr>
<th>ID</th><th>Image</th><th>Name</th><th>Category</th>
<th>Price</th><th>Stock</th><th>Status</th><th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach($products as $p): ?>
<tr>
<td><?= $p['product_id'] ?></td>
<td><?php if($p['image_url']): ?><img src="../uploads/<?= $p['image_url'] ?>" width="60"><?php endif ?></td>
<td><?= $p['product_name'] ?></td>
<td><?= $p['category_name'] ?></td>
<td>Rs. <?= number_format($p['price'],2) ?></td>
<td><?= $p['stock_quantity'] ?></td>
<td><?= $p['status'] ?></td>
<td>
<a href="?edit=<?= $p['product_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
<a href="?delete=<?= $p['product_id'] ?>" class="btn btn-sm btn-danger"
onclick="return confirm('Delete product?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

<?php include "../includes/footer.php"; ?>
</body>
</html>
