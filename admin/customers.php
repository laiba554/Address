<?php
require_once "../includes/auth_admin.php";
require_once "../config/db.php";

/* DELETE CUSTOMER */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
    $stmt->execute([$id]);
}

/* FETCH ALL CUSTOMERS */
$stmt = $conn->query("SELECT * FROM customers ORDER BY created_at DESC");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Customers</h2>

<table border="1" cellpadding="8">
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

    <?php foreach($customers as $c): ?>
    <tr>
        <td><?= $c['customer_id']; ?></td>
        <td><?= htmlspecialchars($c['name']); ?></td>
        <td><?= htmlspecialchars($c['email']); ?></td>
        <td><?= htmlspecialchars($c['cell_phone']); ?></td>
        <td><?= htmlspecialchars($c['work_phone']); ?></td>
        <td><?= $c['date_of_birth']; ?></td>
        <td><?= htmlspecialchars($c['remarks']); ?></td>
        <td>
            <a href="?delete=<?= $c['customer_id']; ?>" 
               onclick="return confirm('Delete this customer?')">
               Delete
            </a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
