<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

$success = "";

/* DELETE CUSTOMER */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
    $stmt->execute([$id]);

    $success = "Customer deleted successfully.";
}

/* FETCH ALL CUSTOMERS */
$stmt = $conn->query("SELECT * FROM customers ORDER BY created_at DESC");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Customers | Admin Panel</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Internal CSS -->
    <style>
        body {
            background-color: #1E1E2F;
            color: #ffffff;
            font-family: Arial, sans-serif;
        }

        .admin-box {
            background-color: #2C2C3E;
            padding: 30px;
            margin-top: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        h2 {
            color: #FFD700;
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

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #FFD700;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<?php include "../includes/navbar.php"; ?>

<div class="container admin-box">

    <h2 class="text-center mb-4">Manage Customers</h2>

    <!-- Success Message -->
    <?php if ($success): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <!-- Customer Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Cell Phone</th>
                    <th>Work Phone</th>
                    <th>Date of Birth</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($customers) > 0): ?>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><?= (int)$c['customer_id']; ?></td>
                            <td><?= htmlspecialchars($c['name']); ?></td>
                            <td><?= htmlspecialchars($c['email']); ?></td>
                            <td><?= htmlspecialchars($c['cell_phone']); ?></td>
                            <td><?= htmlspecialchars($c['work_phone'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($c['date_of_birth']); ?></td>
                            <td><?= htmlspecialchars($c['remarks'] ?? '-'); ?></td>
                            <td>
                                <a href="?delete=<?= (int)$c['customer_id']; ?>"
                                   class="btn btn-delete btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this customer?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No customers found.</td>
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
