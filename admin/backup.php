<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

$message = "";

if (isset($_POST['backup'])) {
    $admin_name = $_SESSION['admin_role'] . " (" . $_SESSION['admin_id'] . ")";

    // Simulate backup filename
    $backupFile = "backup_" . date("Y-m-d_H-i-s") . ".sql";

    // For real project, you can execute mysqldump via PHP (if allowed)
    // exec("mysqldump -u root -pPASSWORD address_jewelers > backups/$backupFile");

    // Insert backup log
    $stmt = $conn->prepare("
        INSERT INTO backup_logs (backup_by, remarks)
        VALUES (?, ?)
    ");
    $stmt->execute([$admin_name, "Manual backup created"]);

    $message = "Backup created successfully (simulation).";
}

/* Fetch backup history */
$stmt = $conn->query("SELECT * FROM backup_logs ORDER BY backup_date DESC");
$backups = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Database Backup</h2>

<?php if($message): ?>
    <p style="color:green"><?= $message; ?></p>
<?php endif; ?>

<form method="post">
    <button type="submit" name="backup">Create Backup</button>
</form>

<hr>

<h3>Backup History</h3>
<table border="1" cellpadding="8">
    <tr>
        <th>Backup ID</th>
        <th>Backup Date</th>
        <th>Backup By</th>
        <th>Remarks</th>
    </tr>
    <?php foreach($backups as $b): ?>
        <tr>
            <td><?= $b['backup_id']; ?></td>
            <td><?= $b['backup_date']; ?></td>
            <td><?= htmlspecialchars($b['backup_by']); ?></td>
            <td><?= htmlspecialchars($b['remarks']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
