<?php
require_once '../includes/auth_check.php';
check_role(['admin','lecturer']);
require_once '../config/database.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['assignment_file'])) {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'] ?? '';
    $due = $_POST['due_date'];

    $uploadDir = '../uploads/assignments/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileName = time() . '_' . basename($_FILES['assignment_file']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $targetPath)) {
        $stmt = $pdo->prepare("INSERT INTO assignments (course_id, title, description, file_path, due_date, uploaded_by) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$course_id, $title, $desc, $targetPath, $due, $_SESSION['user_id']]);
        $message = '<div class="alert alert-success">Assignment uploaded.</div>';
    } else {
        $message = '<div class="alert alert-danger">Upload failed.</div>';
    }
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY course_code")->fetchAll();
require_once '../includes/header.php';
?>
<h2>Upload Assignment</h2>
<?= $message ?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Payment</li>
  </ol>
</nav>
<form method="post" enctype="multipart/form-data" class="card p-4">
    <div class="row">
        <div class="col-md-4">
            <label>Course</label>
            <input list="courseList" name="course_id" class="form-control" required>
            <datalist id="courseList">
                <?php foreach($courses as $c): ?>
                <option value="<?= $c['course_id'] ?>"><?= $c['course_code'] ?> <?= $c['course_name'] ?></option>
                <?php endforeach; ?>
            </datalist>
        </div>
        <div class="col-md-4"><label>Title</label><input type="text" name="title" class="form-control" required></div>
        <div class="col-md-4"><label>Due Date</label><input type="date" name="due_date" class="form-control"></div>
    </div>
    <div class="mt-3">
        <label>Description</label><textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mt-3">
        <label>File (PDF, DOC, etc.)</label>
        <input type="file" name="assignment_file" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Upload</button>
</form>
<?php require_once '../includes/footer.php'; ?>