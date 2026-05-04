<?php
require_once '../includes/auth_check.php';
check_role(['student']);
require_once '../config/database.php';

// We assume student_id is stored in session (done during login)
$student_id = $_SESSION['student_id'];
$semester = $_GET['semester'] ?? 'Sem1';
$academic_year = $_GET['academic_year'] ?? '2026/2027';
$message = '';

// Process enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['courses'])) {
    $selected_courses = $_POST['courses'];
    $errors = [];
    foreach ($selected_courses as $course_id) {
        // Check capacity
        $cap = $pdo->prepare("SELECT max_capacity, (SELECT COUNT(*) FROM enrollments WHERE course_id = ? AND semester = ? AND academic_year = ? AND status='enrolled') AS enrolled FROM courses WHERE course_id = ?");
        $cap->execute([$course_id, $semester, $academic_year, $course_id]);
        $c = $cap->fetch();
        if ($c['enrolled'] >= $c['max_capacity']) {
            $errors[] = "Course ID $course_id is full.";
            continue;
        }
        // Check prerequisite
        $pre = $pdo->prepare("SELECT prerequisite_course_id FROM courses WHERE course_id = ?");
        $pre->execute([$course_id]);
        $preq = $pre->fetchColumn();
        if ($preq) {
            $passed = $pdo->prepare("SELECT grade FROM results WHERE student_id = ? AND course_id = ? AND grade != 'F'");
            $passed->execute([$student_id, $preq]);
            if (!$passed->fetch()) {
                $errors[] = "You haven't passed the prerequisite for course $course_id.";
                continue;
            }
        }
        // All good, enroll
        $ins = $pdo->prepare("INSERT INTO enrollments (student_id, course_id, semester, academic_year, enrollment_date, status) VALUES (?,?,?,?,CURDATE(),'enrolled')");
        $ins->execute([$student_id, $course_id, $semester, $academic_year]);
    }
    $message = empty($errors) ? "<div class='alert alert-success'>Enrollment successful.</div>" : "<div class='alert alert-warning'>" . implode('<br>', $errors) . "</div>";
}

// Fetch available courses for this semester
$courses = $pdo->prepare("SELECT * FROM courses WHERE course_id NOT IN 
    (SELECT course_id FROM enrollments WHERE student_id = ? AND semester = ? AND academic_year = ? AND status='enrolled')");
$courses->execute([$student_id, $semester, $academic_year]);
$available = $courses->fetchAll();
?>
<!DOCTYPE html><html><head><title>Course Registration</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
    <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Payment</li>
  </ol>
</nav>
<div class="container mt-4">
    <h3>Course Registration - <?= $semester ?> <?= $academic_year ?></h3>
    <?= $message ?>
    <form method="post">
        <table class="table table-bordered">
            <thead><tr><th>Select</th><th>Course Code</th><th>Title</th><th>Credits</th><th>Seats Left</th></tr></thead>
            <tbody>
            <?php foreach ($available as $c): 
                $cap_check = $pdo->prepare("SELECT max_capacity - (SELECT COUNT(*) FROM enrollments WHERE course_id = ? AND semester = ? AND academic_year = ? AND status='enrolled') AS remaining FROM courses WHERE course_id = ?");
                $cap_check->execute([$c['course_id'], $semester, $academic_year, $c['course_id']]);
                $rem = $cap_check->fetchColumn();
                if ($rem <= 0) continue; // hide full courses
            ?>
                <tr>
                    <td><input type="checkbox" name="courses[]" value="<?= $c['course_id'] ?>"></td>
                    <td><?= $c['course_code'] ?></td>
                    <td><?= $c['course_name'] ?></td>
                    <td><?= $c['credit_hours'] ?></td>
                    <td><?= $rem ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Enroll Selected Courses</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>
</body></html>