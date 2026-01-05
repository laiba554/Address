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

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f7f5;
        color: #2c2c2c;
    }

    .admin-wrapper {
        max-width: 1280px;
        margin: 40px auto;
        padding: 0 15px;
    }

    .admin-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 35px 40px;
        box-shadow: 0 30px 70px rgba(0,0,0,0.08);
        border: 1px solid #e2ece7;
    }

    .page-header {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 30px;
    }

    .page-header h2 {
        font-size: 30px;
        font-weight: 700;
        background: linear-gradient(90deg, #0f3d2e, #2f6f5c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0;
    }

    .page-header p {
        font-size: 14px;
        color: #6b7d77;
        margin: 0;
    }

    .alert-success {
        background: #e6f4ef;
        color: #0f3d2e;
        border: none;
        border-radius: 14px;
        font-weight: 500;
        padding: 14px 18px;
        margin-bottom: 25px;
    }

    .table-container {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2ece7;
    }

    table {
        margin: 0;
        font-size: 14px;
    }

    thead {
        background: #f3f8f6;
    }

    th {
        padding: 18px 14px;
        font-weight: 600;
        color: #0f3d2e;
        text-align: center;
        border-bottom: 1px solid #e2ece7;
        white-space: nowrap;
    }

    td {
        padding: 16px 14px;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #eef4f2;
        color: #333;
    }

    tbody tr:hover {
        background: #f7fbfa;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    .text-muted-small {
        color: #8a9a95;
        font-size: 13px;
    }

    .btn-delete {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff;
        border: none;
        padding: 7px 18px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.25s ease;
        text-decoration: none;
    }

    .btn-delete:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(231,76,60,0.35);
        color: #fff;
    }

    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #1abc9c, #16a085);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #eaf2ef;
    }

    @media (max-width: 768px) {
        .admin-card {
            padding: 25px 20px;
        }

        .page-header h2 {
            font-size: 26px;
        }

        th, td {
            font-size: 13px;
            padding: 12px;
        }
    }
</style>
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="admin-wrapper">
    <div class="admin-card">

        <div class="page-header">
            <h2>Manage Customers</h2>
            <p>View, manage, and maintain registered customer accounts</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success text-center">
                <?= htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="table-container table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Cell</th>
                        <th>Work</th>
                        <th>DOB</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($customers): ?>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><?= (int)$c['customer_id']; ?></td>
                            <td><?= htmlspecialchars($c['name']); ?></td>
                            <td><?= htmlspecialchars($c['email']); ?></td>
                            <td><?= htmlspecialchars($c['cell_phone']); ?></td>
                            <td><?= htmlspecialchars($c['work_phone'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($c['date_of_birth']); ?></td>
                            <td class="text-muted-small">
                                <?= htmlspecialchars($c['remarks'] ?? '—'); ?>
                            </td>
                            <td>
                                <a href="?delete=<?= (int)$c['customer_id']; ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this customer?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-muted text-center py-4">
                            No customers found.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
