<?php
require_once '../includes/auth_check.php';
check_role(['admin','finance']);
require_once '../config/database.php';
require_once '../config/constants.php';

$semester = $_GET['semester'] ?? 'Sem1';
$academic_year = $_GET['academic_year'] ?? '2026/2027';

// Fetch all students
$students = $pdo->query("SELECT student_id, first_name, last_name FROM students WHERE status = 'active' ORDER BY last_name");
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="fee_report_'.$semester.'_'.$academic_year.'.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Student ID', 'Name', 'Tuition', 'Paid', 'Balance']);
    // loop through students and write rows
    // (same loop used later in table)
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html><head><title>Fee Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
    <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Payment</li>
  </ol>
</nav>
<div class="container mt-4">
    <h3>Fee Report (<?= $semester ?> - <?= $academic_year ?>)</h3>
    <form class="row g-3 mb-4" method="get">
        <div class="col-auto"><label>Semester</label><select name="semester" class="form-select"><option <?= $semester=='Sem1'?'selected':'' ?>>Sem1</option><option <?= $semester=='Sem2'?'selected':'' ?>>Sem2</option><option <?= $semester=='Summer'?'selected':'' ?>>Summer</option></select></div>
        <div class="col-auto"><label>Academic Year</label><input type="text" name="academic_year" class="form-control" value="<?= $academic_year ?>"></div>
        <div class="col-auto align-self-end"><button type="submit" class="btn btn-info">Filter</button></div>
    </form>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Student ID</th><th>Name</th><th>Tuition Fee</th><th>Total Paid</th><th>Balance</th></tr></thead>
        <tbody>
        <?php while ($stu = $students->fetch()): 
            $total_paid_stmt = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE student_id = ? AND semester = ? AND academic_year = ?");
            $total_paid_stmt->execute([$stu['student_id'], $semester, $academic_year]);
            $total_paid = $total_paid_stmt->fetchColumn();
            $balance = TUITION_PER_SEMESTER - $total_paid;
        ?>
            <tr>
                <td><?= $stu['student_id'] ?></td>
                <td><?= $stu['first_name'].' '.$stu['last_name'] ?></td>
                <td><?= number_format(TUITION_PER_SEMESTER) ?></td>
                <td><?= number_format($total_paid) ?></td>
                <td class="<?= $balance > 0 ? 'text-danger' : 'text-success' ?>"><?= number_format($balance) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <a href="fee_reports.php?export=csv&semester=<?= $semester ?>&academic_year=<?= $academic_year ?>" class="btn btn-outline-success">
    <i class="bi bi-download"></i> Export CSV
</a>
</div>
<?php require_once '../includes/footer.php'; ?>
</body></html>