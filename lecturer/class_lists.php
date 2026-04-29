<?php
require_once '../includes/auth_check.php';
check_role(['admin','lecturer']);
require_once '../config/database.php';

$semester = $_GET['semester'] ?? 'Sem1';
$academic_year = $_GET['academic_year'] ?? '2026/2027';
$course_id = $_GET['course_id'] ?? null;
$courses = $pdo->query("SELECT * FROM courses ORDER BY course_code");

$students = [];
if ($course_id) {
    $stmt = $pdo->prepare("SELECT s.student_id, s.first_name, s.last_name FROM enrollments e JOIN students s ON e.student_id = s.student_id WHERE e.course_id = ? AND e.semester = ? AND e.academic_year = ? AND e.status = 'enrolled' ORDER BY s.last_name");
    $stmt->execute([$course_id, $semester, $academic_year]);
    $students = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html><head><title>Class List</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-4">
    <h3>Class List</h3>
    <form method="get" class="row g-3">
        <div class="col-md-4"><label>Course</label><select name="course_id" class="form-select" required><option value="">-- Select --</option><?php foreach($courses as $c): ?><option value="<?= $c['course_id'] ?>" <?= $course_id==$c['course_id']?'selected':'' ?>><?= $c['course_code'].' - '.$c['course_name'] ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label>Semester</label><select name="semester" class="form-select"><option <?= $semester=='Sem1'?'selected':'' ?>>Sem1</option><option <?= $semester=='Sem2'?'selected':'' ?>>Sem2</option><option <?= $semester=='Summer'?'selected':'' ?>>Summer</option></select></div>
        <div class="col-md-3"><label>Academic Year</label><input type="text" name="academic_year" class="form-control" value="<?= $academic_year ?>"></div>
        <div class="col-md-2 align-self-end"><button type="submit" class="btn btn-info">Load</button></div>
    </form>
    <?php if ($course_id && count($students)): ?>
        <table class="table table-striped mt-4">
            <thead><tr><th>#</th><th>Student ID</th><th>Name</th></tr></thead>
            <tbody>
            <?php $i=1; foreach($students as $s): ?>
                <tr><td><?= $i++ ?></td><td><?= $s['student_id'] ?></td><td><?= $s['first_name'].' '.$s['last_name'] ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($course_id): ?>
        <div class="alert alert-warning mt-3">No enrollment found.</div>
    <?php endif; ?>
</div>
</body></html>