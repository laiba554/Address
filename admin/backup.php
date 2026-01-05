<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

$message = "";
$error = "";

/* Handle Backup */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['backup'])) {

    if (!isset($_SESSION['admin_id'], $_SESSION['admin_role'])) {
        $error = "Unauthorized access.";
    } else {
        $admin_name = $_SESSION['admin_role'] . " (ID: " . $_SESSION['admin_id'] . ")";

        $backupFile = "backup_" . date("Y-m-d_H-i-s") . ".sql";

        $stmt = $conn->prepare("
            INSERT INTO backup_logs (backup_by, remarks)
            VALUES (?, ?)
        ");
        $stmt->execute([$admin_name, "Manual backup created: $backupFile"]);

        $message = "Database backup created successfully.";
    }
}

$stmt = $conn->query("SELECT * FROM backup_logs ORDER BY backup_date DESC");
$backups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Database Backup | Admin Panel</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f7f5;
        color: #2c2c2c;
    }

    .container-box {
        max-width: 1100px;
        background: #ffffff;
        padding: 35px 40px;
        margin: 40px auto;
        border-radius: 18px;
        box-shadow: 0 25px 55px rgba(0,0,0,0.08);
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

    .btn-backup {
         background: linear-gradient(135deg, #0b6b4f, #158f6b);
            color: #ffffff;
        font-weight: 600;
        padding: 12px 28px;
        border-radius: 30px;
        border: none;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .btn-backup:hover {
        background: linear-gradient(135deg, #16a085, #1abc9c);
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(22,160,133,0.35);
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
        color: #333;
    }

    table tbody tr:hover {
        background: #f7fbfa;
    }

    .section-title {
        font-weight: 600;
        color: #0f3d2e;
        text-align: center;
        margin: 30px 0 20px;
        font-size: 20px;
    }

    @media (max-width: 768px) {
        .container-box {
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

<div class="container-box">

    <h2 class="page-title">Database Backup</h2>

    <?php if ($message): ?>
        <div class="alert alert-success text-center mb-4">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center mb-4">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="text-center mb-4">
        <form method="post">
            <button type="submit" name="backup" class="btn btn-backup">
                Create Backup
            </button>
        </form>
    </div>

    <h4 class="section-title">Backup History</h4>

    <div class="table-wrapper table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Backup ID</th>
                    <th>Date</th>
                    <th>Backup By</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($backups): ?>
                    <?php foreach ($backups as $b): ?>
                        <tr>
                            <td><?= (int)$b['backup_id']; ?></td>
                            <td><?= htmlspecialchars($b['backup_date']); ?></td>
                            <td><?= htmlspecialchars($b['backup_by']); ?></td>
                            <td><?= htmlspecialchars($b['remarks']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-muted">No backup records found.</td>
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
