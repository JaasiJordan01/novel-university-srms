<?php
require_once '../includes/auth_check.php';
check_role(['student']);
require_once '../config/database.php';

$student_id = $_SESSION['student_id'];
$semester = $_GET['semester'] ?? null;
$academic_year = $_GET['academic_year'] ?? null;

$where = "r.student_id = ?";
$params = [$student_id];
if ($semester) { $where .= " AND r.semester = ?"; $params[] = $semester; }
if ($academic_year) { $where .= " AND r.academic_year = ?"; $params[] = $academic_year; }

$stmt = $pdo->prepare("SELECT r.*, c.course_code, c.course_name, c.credit_hours 
                       FROM results r
                       JOIN courses c ON r.course_id = c.course_id
                       WHERE $where
                       ORDER BY r.academic_year DESC, r.semester");
$stmt->execute($params);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html><head><title>My Results</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
    <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Payment</li>
  </ol>
</nav>
<div class="container mt-4">
    <h3>My Results</h3>
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3"><label>Semester</label><select name="semester" class="form-select"><option value="">All</option><option <?= $semester=='Sem1'?'selected':'' ?>>Sem1</option><option <?= $semester=='Sem2'?'selected':'' ?>>Sem2</option><option <?= $semester=='Summer'?'selected':'' ?>>Summer</option></select></div>
        <div class="col-md-3"><label>Academic Year</label><input type="text" name="academic_year" class="form-control" placeholder="2026/2027" value="<?= $academic_year ?>"></div>
        <div class="col-md-2 align-self-end"><button type="submit" class="btn btn-primary">Filter</button></div>
    </form>
    <table class="table table-bordered">
        <thead><tr><th>Course Code</th><th>Course Name</th><th>Credit Hours</th><th>CAT</th><th>Exam</th><th>Total</th><th>Grade</th><th>Semester</th><th>Year</th></tr></thead>
        <tbody>
        <?php foreach ($results as $r): ?>
            <tr>
                <td><?= $r['course_code'] ?></td>
                <td><?= $r['course_name'] ?></td>
                <td><?= $r['credit_hours'] ?></td>
                <td><?= $r['cat_marks'] ?></td>
                <td><?= $r['exam_marks'] ?></td>
                <td><?= $r['total_marks'] ?></td>
                <td><?= $r['grade'] ?></td>
                <td><?= $r['semester'] ?></td>
                <td><?= $r['academic_year'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<a href="../dashboard.php" class="btn btn-secondary">Back</a>
<?php require_once '../includes/footer.php'; ?>
</body></html>