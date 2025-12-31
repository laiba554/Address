<?php
require_once "../includes/auth_customer.php";
require_once "../config/db.php";

/* FETCH CATEGORIES */
$catStmt = $conn->query("SELECT * FROM categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

/* SEARCH & FILTER */
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products | Online Store</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
        }

        .page-container {
            max-width: 1200px;
            margin: 40px auto;
        }

        .page-title {
            font-weight: 600;
            color: #1e1e2f;
            margin-bottom: 25px;
        }

        .filter-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .product-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform .3s ease;
        }

        .product-card:hover {
            transform: translateY(-6px);
        }

        .product-img {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }

        .product-body {
            padding: 15px;
        }

        .product-name {
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .category {
            font-size: 13px;
            color: #777;
        }

        .price {
            font-weight: 600;
            font-size: 16px;
            color: #1e1e2f;
        }

        .stock {
            font-size: 13px;
            color: green;
        }

        .btn-cart {
            background: #1e1e2f;
            color: #fff;
            border-radius: 8px;
            font-size: 14px;
        }

        .btn-cart:hover {
            background: #000;
            color: #fff;
        }

        .no-products {
            background: #fff;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

<div class="page-container">

    <h3 class="page-title">Our Products</h3>

    <!-- FILTER SECTION -->
    <div class="filter-box">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label">Search Product</label>
                <input type="text" name="search" class="form-control"
                       placeholder="Search by product name"
                       value="<?= $_GET['search'] ?? '' ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id']; ?>"
                            <?= (($_GET['category_id'] ?? '') == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-dark">Search</button>
            </div>
        </form>
    </div>

    <!-- PRODUCTS GRID -->
    <div class="row g-4">

        <?php if (empty($products)): ?>
            <div class="col-12">
                <div class="no-products">
                    <h5>No products found</h5>
                    <p class="text-muted">Try adjusting your search or category.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($products as $p): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="product-card h-100">
                    <?php if ($p['image_url']): ?>
                        <img src="../uploads/<?= $p['image_url']; ?>" class="product-img">
                    <?php else: ?>
                        <img src="../assets/no-image.png" class="product-img">
                    <?php endif; ?>

                    <div class="product-body">
                        <div class="product-name"><?= htmlspecialchars($p['product_name']); ?></div>
                        <div class="category"><?= htmlspecialchars($p['category_name']); ?></div>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <div class="price">₹<?= number_format($p['price'], 2); ?></div>
                                <div class="stock">In Stock: <?= $p['stock_quantity']; ?></div>
                            </div>
                        </div>

                        <a href="cart.php?add=<?= $p['product_id']; ?>"
                           class="btn btn-cart w-100 mt-3">
                            Add to Cart
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>

</body>
</html>
