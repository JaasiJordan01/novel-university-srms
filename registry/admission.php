<?php
require_once '../includes/auth_check.php';
check_role(['admin','registry']);
require_once '../config/database.php';
$message = '';

// We'll display a list of all students (or applicants) and allow status changes.
// For now, just show students; later you can expand to handle applications.
$students = $pdo->query("SELECT * FROM students ORDER BY enrollment_year DESC, last_name")->fetchAll();

require_once '../includes/header.php';
?>
<h2>Admissions Management</h2>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr><th>Student ID</th><th>Name</th><th>Program</th><th>Year</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
            <tr>
                <td><?= $s['student_id'] ?></td>
                <td><?= $s['first_name'] .' '. $s['last_name'] ?></td>
                <td><?= $s['program'] ?></td>
                <td><?= $s['enrollment_year'] ?></td>
                <td><span class="badge bg-<?= $s['status']=='active'?'success':'secondary' ?>"><?= $s['status'] ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once '../includes/footer.php'; ?>