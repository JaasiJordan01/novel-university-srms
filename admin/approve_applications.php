<?php
require_once '../includes/auth_check.php';
check_role(['admin']);
require_once '../config/database.php';
$message = '';

// Approve application
if (isset($_GET['approve'])) {
    $app_id = $_GET['approve'];
    // Generate a unique token
    $token = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare("UPDATE applicants SET status='approved', token=? WHERE applicant_id=?");
    $stmt->execute([$token, $app_id]);
    // Build the link that student must visit to set password
    $set_password_link = "http://".$_SERVER['HTTP_HOST']."/usrsms/set_password.php?token=".$token;
    $message = "<div class='alert alert-success'>Application approved. The student must set their password using this link:<br><code>$set_password_link</code></div>";
}

// Reject application
if (isset($_GET['reject'])) {
    $app_id = $_GET['reject'];
    $stmt = $pdo->prepare("UPDATE applicants SET status='rejected' WHERE applicant_id=?");
    $stmt->execute([$app_id]);
    $message = "<div class='alert alert-warning'>Application rejected.</div>";
}

// Fetch all applicants
$apps = $pdo->query("SELECT * FROM applicants ORDER BY created_at DESC")->fetchAll();

require_once '../includes/header.php';
?>
<h2>Manage Applications</h2>
<?= $message ?>
<table class="table table-hover">
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Program</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($apps as $a): ?>
        <tr>
            <td>APP-<?= $a['applicant_id'] ?></td>
            <td><?= $a['first_name'].' '.$a['last_name'] ?></td>
            <td><?= $a['email'] ?></td>
            <td><?= $a['program_applied'] ?></td>
            <td><span class="badge bg-<?= $a['status']=='approved'?'success':($a['status']=='rejected'?'danger':'secondary') ?>"><?= $a['status'] ?></span></td>
            <td>
                <?php if ($a['status'] == 'pending'): ?>
                    <a href="?approve=<?= $a['applicant_id'] ?>" class="btn btn-sm btn-success">Approve</a>
                    <a href="?reject=<?= $a['applicant_id'] ?>" class="btn btn-sm btn-danger">Reject</a>
                <?php else: ?>
                    <?= $a['status']=='approved' ? 'Token sent' : 'Rejected' ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require_once '../includes/footer.php'; ?>