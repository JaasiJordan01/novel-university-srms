<?php
require_once '../includes/auth_check.php';
check_role(['student']);
require_once '../config/database.php';
$student_id = $_SESSION['student_id'];

// Get enrolled courses
$stmt = $pdo->prepare("SELECT c.course_id, c.course_code, c.course_name FROM enrollments e JOIN courses c ON e.course_id=c.course_id WHERE e.student_id=? AND e.status='enrolled'");
$stmt->execute([$student_id]);
$enrolled = $stmt->fetchAll();

// Get assignments for those courses
$course_ids = array_column($enrolled, 'course_id');
if (empty($course_ids)) $course_ids = [0];
$placeholders = implode(',', array_fill(0, count($course_ids), '?'));
$stmt_asgn = $pdo->prepare("SELECT a.*, c.course_code, c.course_name FROM assignments a JOIN courses c ON a.course_id=c.course_id WHERE a.course_id IN ($placeholders) ORDER BY a.created_at DESC");
$stmt_asgn->execute($course_ids);
$assignments = $stmt_asgn->fetchAll();

require_once '../includes/header.php';
?>
<h2>My Assignments</h2>
<table class="table">
 <thead><tr><th>Course</th><th>Title</th><th>Due Date</th><th>File</th></tr></thead>
 <tbody>
 <?php foreach ($assignments as $a): ?>
 <tr>
   <td><?= $a['course_code'] ?></td>
   <td><?= $a['title'] ?></td>
   <td><?= $a['due_date'] ?></td>
   <td><a href="<?= $a['file_path'] ?>" class="btn btn-sm btn-primary" download>Download</a></td>
 </tr>
 <?php endforeach; ?>
 </tbody>
</table>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Payment</li>
  </ol>
</nav>
<?php require_once '../includes/footer.php'; ?>