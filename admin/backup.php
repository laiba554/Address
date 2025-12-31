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

        // Simulated backup filename
        $backupFile = "backup_" . date("Y-m-d_H-i-s") . ".sql";

        // Log backup action
        $stmt = $conn->prepare("
            INSERT INTO backup_logs (backup_by, remarks)
            VALUES (?, ?)
        ");
        $stmt->execute([$admin_name, "Manual backup created: $backupFile"]);

        $message = "Database backup created successfully.";
    }
}

/* Fetch backup history */
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

    <!-- Internal Theme CSS -->
    <style>
        body {
            background-color: #1E1E2F;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .container-box {
            background-color: #2C2C3E;
            padding: 30px;
            margin-top: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }

        h2, h4 {
            color: #FFD700;
        }

        .btn-backup {
            background-color: #FFD700;
            color: #1E1E2F;
            font-weight: bold;
            border: none;
        }

        .btn-backup:hover {
            background-color: #e6c200;
        }

        table {
            background-color: #1E1E2F;
            color: #fff;
            border-radius: 8px;
            overflow: hidden;
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

<div class="container container-box">

    <h2 class="text-center mb-4">Database Backup</h2>

    <!-- Messages -->
    <?php if ($message): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Backup Button -->
    <form method="post" class="text-center mb-4">
        <button type="submit" name="backup" class="btn btn-backup px-4 py-2">
            Create Backup
        </button>
    </form>

    <hr style="border-color:#444;">

    <!-- Backup History -->
    <h4 class="text-center mb-3">Backup History</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Backup ID</th>
                    <th>Date</th>
                    <th>Backup By</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($backups) > 0): ?>
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
                        <td colspan="4">No backup records found.</td>
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
