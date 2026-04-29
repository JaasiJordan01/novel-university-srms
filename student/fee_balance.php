<?php
require_once '../includes/auth_check.php';
check_role(['student']);
require_once '../config/database.php';
require_once '../config/constants.php';

$student_id = $_SESSION['student_id'];
$semester = $_GET['semester'] ?? 'Sem1';
$academic_year = $_GET['academic_year'] ?? '2026/2027';

// Total payments
$paid_stmt = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE student_id = ? AND semester = ? AND academic_year = ?");
$paid_stmt->execute([$student_id, $semester, $academic_year]);
$total_paid = $paid_stmt->fetchColumn();
$balance = TUITION_PER_SEMESTER - $total_paid;
?>
<!DOCTYPE html>
<html><head><title>Fee Balance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-4">
    <h3>My Fee Balance</h3>
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3"><label>Semester</label><select name="semester" class="form-select"><option <?= $semester=='Sem1'?'selected':'' ?>>Sem1</option><option <?= $semester=='Sem2'?'selected':'' ?>>Sem2</option><option <?= $semester=='Summer'?'selected':'' ?>>Summer</option></select></div>
        <div class="col-md-3"><label>Academic Year</label><input type="text" name="academic_year" class="form-control" value="<?= $academic_year ?>"></div>
        <div class="col-md-2 align-self-end"><button type="submit" class="btn btn-info">Refresh</button></div>
    </form>
    <div class="card">
        <div class="card-header">Summary for <?= $semester ?> <?= $academic_year ?></div>
        <div class="card-body">
            <p><strong>Tuition Fee:</strong> <?= number_format(TUITION_PER_SEMESTER) ?></p>
            <p><strong>Total Paid:</strong> <?= number_format($total_paid) ?></p>
            <p><strong>Outstanding Balance:</strong> <span class="text-<?= $balance > 0 ? 'danger' : 'success' ?> fw-bold"><?= number_format($balance) ?></span></p>
        </div>
    </div>
</div>
</body></html>