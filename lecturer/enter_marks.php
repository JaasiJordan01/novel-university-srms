<?php
require_once '../includes/auth_check.php';
check_role(['admin','lecturer']);
require_once '../config/database.php';
$message = '';

// Step 1: choose course & semester
$course_id = $_GET['course_id'] ?? null;
$semester = $_GET['semester'] ?? 'Sem1';
$academic_year = $_GET['academic_year'] ?? '2026/2027';

// Fetch courses where this lecturer might teach (we could add a lecturer_course table but here we show all courses)
$courses = $pdo->query("SELECT course_id, course_code, course_name FROM courses ORDER BY course_code");

// If a course is selected, fetch enrolled students and their existing marks
if ($course_id && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cat_marks'])) {
    // Process marks entry
    $student_ids = $_POST['student_id'];
    $cat_marks = $_POST['cat_marks'];
    $exam_marks = $_POST['exam_marks'];
    $count = 0;
    foreach ($student_ids as $i => $sid) {
        $cat = $cat_marks[$i];
        $exam = $exam_marks[$i];
        // calculate total and grade
        $total = $cat + $exam;
        if ($total >= 70) $grade = 'A';
        elseif ($total >= 60) $grade = 'B';
        elseif ($total >= 50) $grade = 'C';
        elseif ($total >= 40) $grade = 'D';
        else $grade = 'F';

        // check if result already exists
        $exists = $pdo->prepare("SELECT result_id FROM results WHERE student_id = ? AND course_id = ? AND semester = ? AND academic_year = ?");
        $exists->execute([$sid, $course_id, $semester, $academic_year]);
        if ($exists->fetch()) {
            $upd = $pdo->prepare("UPDATE results SET cat_marks=?, exam_marks=?, grade=?, entered_by=? WHERE student_id=? AND course_id=? AND semester=? AND academic_year=?");
            $upd->execute([$cat, $exam, $grade, $_SESSION['user_id'], $sid, $course_id, $semester, $academic_year]);
        } else {
            $ins = $pdo->prepare("INSERT INTO results (student_id, course_id, semester, academic_year, cat_marks, exam_marks, grade, entered_by) VALUES (?,?,?,?,?,?,?,?)");
            $ins->execute([$sid, $course_id, $semester, $academic_year, $cat, $exam, $grade, $_SESSION['user_id']]);
        }
        $count++;
    }
    $message = "<div class='alert alert-success'>Marks saved for $count students.</div>";
}

// If course selected, get enrollment list
$students = [];
if ($course_id) {
    $stmt = $pdo->prepare("SELECT s.student_id, s.first_name, s.last_name, 
                                  r.cat_marks, r.exam_marks, r.grade
                           FROM enrollments e
                           JOIN students s ON e.student_id = s.student_id
                           LEFT JOIN results r ON r.student_id = e.student_id AND r.course_id = e.course_id AND r.semester = e.semester AND r.academic_year = e.academic_year
                           WHERE e.course_id = ? AND e.semester = ? AND e.academic_year = ? AND e.status = 'enrolled'
                           ORDER BY s.last_name");
    $stmt->execute([$course_id, $semester, $academic_year]);
    $students = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html><head><title>Enter Marks</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
    <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Payment</li>
  </ol>
</nav>
<div class="container mt-4">
    <h3>Enter Marks</h3>
    <!-- Selection form -->
    <form class="row g-3 mb-4" method="get">
        <div class="col-md-4">
            <label>Course</label>
            <select name="course_id" class="form-select" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['course_id'] ?>" <?= $course_id == $c['course_id'] ? 'selected' : '' ?>><?= $c['course_code'].' - '.$c['course_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label>Semester</label>
            <select name="semester" class="form-select">
                <option <?= $semester=='Sem1'?'selected':'' ?>>Sem1</option>
                <option <?= $semester=='Sem2'?'selected':'' ?>>Sem2</option>
                <option <?= $semester=='Summer'?'selected':'' ?>>Summer</option>
            </select>
        </div>
        <div class="col-md-3">
            <label>Academic Year</label>
            <input type="text" name="academic_year" class="form-control" value="<?= $academic_year ?>">
        </div>
        <div class="col-md-2 align-self-end"><button type="submit" class="btn btn-secondary">Load</button></div>
    </form>

    <?php if ($course_id && !empty($students)): ?>
        <?= $message ?>
        <form method="post">
            <input type="hidden" name="course_id" value="<?= $course_id ?>">
            <table class="table table-bordered">
                <thead><tr><th>Student ID</th><th>Name</th><th>CAT (30%)</th><th>Exam (70%)</th><th>Existing Grade</th></tr></thead>
                <tbody>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td><input type="hidden" name="student_id[]" value="<?= $s['student_id'] ?>"><?= $s['student_id'] ?></td>
                        <td><?= $s['first_name'].' '.$s['last_name'] ?></td>
                        <td><input type="number" name="cat_marks[]" class="form-control" value="<?= $s['cat_marks'] ?? '' ?>" step="0.01" min="0" max="30" placeholder="0-30"></td>
                        <td><input type="number" name="exam_marks[]" class="form-control" value="<?= $s['exam_marks'] ?? '' ?>" step="0.01" min="0" max="70" placeholder="0-70"></td>
                        <td><?= $s['grade'] ?? 'N/A' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Save Marks</button>
        </form>
    <?php elseif ($course_id): ?>
        <div class="alert alert-info">No students enrolled in this course for the selected semester.</div>
    <?php endif; ?>
    <a href="../dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
<?php require_once '../includes/footer.php'; ?>
</body></html>