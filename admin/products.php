<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

/* FETCH CATEGORIES */
$categories = $conn->query("
    SELECT * FROM categories ORDER BY category_name
")->fetchAll(PDO::FETCH_ASSOC);

/* EDIT MODE FETCH */
$editProduct = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ADD PRODUCT */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_product'])) {
    $category_id = $_POST['category_id'];
    $name = trim($_POST['product_name']);
    $desc = trim($_POST['product_description']);
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];
    $status = $_POST['status'];

    /* VALIDATIONS */
    $errors = [];

    if ($price < 0) {
        $errors[] = "Price cannot be negative.";
    }

    if ($stock < 0) {
        $errors[] = "Stock quantity cannot be negative.";
    }

    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['png','jpg','jpeg'])) {
            $errors[] = "Only PNG, JPG, JPEG images are allowed.";
        } else {
            $imageName = time() . "_" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $imageName);
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO products
            (category_id, product_name, product_description, price, stock_quantity, image_url, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$category_id,$name,$desc,$price,$stock,$imageName,$status]);

        header("Location: products.php");
        exit;
    }
}

/* UPDATE PRODUCT */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_product'])) {
    $id = $_POST['product_id'];
    $category_id = $_POST['category_id'];
    $name = trim($_POST['product_name']);
    $desc = trim($_POST['product_description']);
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];
    $status = $_POST['status'];
    $oldImage = $_POST['old_image'];

    /* VALIDATIONS */
    $errors = [];

    if ($price < 0) {
        $errors[] = "Price cannot be negative.";
    }

    if ($stock < 0) {
        $errors[] = "Stock quantity cannot be negative.";
    }

    $imageName = $oldImage;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['png','jpg','jpeg'])) {
            $errors[] = "Only PNG, JPG, JPEG images are allowed.";
        } else {
            if ($oldImage && file_exists("../uploads/".$oldImage)) {
                unlink("../uploads/".$oldImage);
            }
            $imageName = time()."_".uniqid().".".$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/".$imageName);
        }
    }

    if (empty($errors)) {
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
        $stmt->execute([$category_id,$name,$desc,$price,$stock,$imageName,$status,$id]);

        header("Location: products.php");
        exit;
    }
}

/* DELETE PRODUCT */
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
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Products | Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body{
    background:#f4f7f5;
    font-family:'Poppins',sans-serif;
    color:#2c2c2c;
}

.page-wrapper{
    max-width:1300px;
    margin:40px auto;
}

h2{
    font-weight:700;
    font-size:30px;
    background:linear-gradient(90deg,#0f3d2e,#3f8f77);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    margin-bottom:25px;
}

.card-box{
    background:#ffffff;
    border-radius:20px;
    padding:30px;
    border:1px solid #e2ece7;
    box-shadow:0 18px 45px rgba(0,0,0,0.07);
    margin-bottom:40px;
}

label{
    font-size:13px;
    font-weight:600;
    color:#0f3d2e;
}

input,textarea,select{
    border-radius:12px!important;
    border:1px solid #cfe1da!important;
    font-size:14px!important;
}

.primary-btn{
     background: linear-gradient(135deg, #0b6b4f, #158f6b);
            color: #ffffff;
    border:none;
    color:#ffffff;
    font-weight:600;
    padding:10px 28px;
    border-radius:30px;
    transition:0.25s ease;
}

.primary-btn:hover{
    background:linear-gradient(135deg,#3f907a,#5db49c);
    box-shadow:0 10px 25px rgba(79,161,138,0.35);
}

/* Table */
.table-wrapper{
    background:#ffffff;
    border-radius:20px;
    padding:25px;
    border:1px solid #e2ece7;
    box-shadow:0 18px 45px rgba(0,0,0,0.07);
}

table th{
    background:#f0f6f3;
    color:#0f3d2e;
    font-weight:600;
}

.action-btn{
    padding:5px 14px;
    font-size:13px;
    border-radius:20px;
}

.error-msg{
    color:red;
    font-weight:600;
    margin-bottom:15px;
}
</style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="page-wrapper container-fluid">

<h2><?= $editProduct ? 'Update Product' : 'Add New Product' ?></h2>

<div class="card-box">

<?php if (!empty($errors)): ?>
<div class="error-msg">
<?php foreach($errors as $err) echo $err."<br>"; ?>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="product_id" value="<?= $editProduct['product_id'] ?? '' ?>">
<input type="hidden" name="old_image" value="<?= $editProduct['image_url'] ?? '' ?>">

<div class="row g-4">
<div class="col-md-4">
<label>Category</label>
<select name="category_id" class="form-control">
<?php foreach($categories as $c): ?>
<option value="<?= $c['category_id'] ?>" <?= ($editProduct && $editProduct['category_id']==$c['category_id'])?'selected':'' ?>>
<?= $c['category_name'] ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-4">
<label>Product Name</label>
<input type="text" name="product_name" class="form-control" value="<?= $editProduct['product_name'] ?? '' ?>" required>
</div>

<div class="col-md-4">
<label>Price (PKR)</label>
<input type="number" step="0.01" name="price" class="form-control" value="<?= $editProduct['price'] ?? '' ?>" required>
</div>

<div class="col-md-4">
<label>Stock Quantity</label>
<input type="number" name="stock_quantity" class="form-control" value="<?= $editProduct['stock_quantity'] ?? '' ?>" required>
</div>

<div class="col-md-4">
<label>Status</label>
<select name="status" class="form-control">
<option <?= ($editProduct['status']??'')=='Available'?'selected':'' ?>>Available</option>
<option <?= ($editProduct['status']??'')=='Out of Stock'?'selected':'' ?>>Out of Stock</option>
</select>
</div>

<div class="col-md-4">
<label>Product Image</label>
<input type="file" name="image" class="form-control">
</div>

<div class="col-12">
<label>Description</label>
<textarea name="product_description" class="form-control"><?= $editProduct['product_description'] ?? '' ?></textarea>
</div>

<div class="col-12 text-end">
<button type="submit" name="<?= $editProduct ? 'update_product' : 'add_product' ?>" class="primary-btn">
<?= $editProduct ? 'Update Product' : 'Add Product' ?>
</button>
</div>
</div>
</form>
</div>

<div class="table-wrapper table-responsive">
<table class="table table-borderless align-middle">
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
<td><?php if($p['image_url']): ?><img src="../uploads/<?= $p['image_url'] ?>" width="60"><?php endif; ?></td>
<td><?= $p['product_name'] ?></td>
<td><?= $p['category_name'] ?></td>
<td>PKR <?= number_format($p['price'],2) ?></td>
<td><?= $p['stock_quantity'] ?></td>
<td><?= $p['status'] ?></td>
<td>
<a href="?edit=<?= $p['product_id'] ?>" class="btn btn-outline-success action-btn">Edit</a>
<a href="?delete=<?= $p['product_id'] ?>" class="btn btn-outline-danger action-btn"
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
