<?php
require_once '../includes/auth_check.php';
check_role(['admin','registry']);
require_once '../config/database.php';
$message = '';

// Add course
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['course_code'];
    $name = $_POST['course_name'];
    $credits = $_POST['credit_hours'];
    $dept = $_POST['department'];
    $capacity = $_POST['max_capacity'];
    $prereq = $_POST['prerequisite_course_id'] ?: null;

    $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_name, credit_hours, department, max_capacity, prerequisite_course_id) VALUES (?,?,?,?,?,?)");
    try {
        $stmt->execute([$code, $name, $credits, $dept, $capacity, $prereq]);
        $message = "<div class='alert alert-success'>Course added.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// List all courses with prerequisite
$courses = $pdo->query("SELECT c.*, pc.course_code AS prereq_code 
                        FROM courses c 
                        LEFT JOIN courses pc ON c.prerequisite_course_id = pc.course_id 
                        ORDER BY c.course_code");
?>
<!DOCTYPE html>
<html><head><title>Manage Courses</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-4">
    <h3>Course Management</h3>
    <?= $message ?>
    <div class="card mb-4">
        <div class="card-header">Add New Course</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-2"><input type="text" name="course_code" class="form-control" placeholder="Code" required></div>
                <div class="col-md-2"><input type="text" name="course_name" class="form-control" placeholder="Course Name" required></div>
                <div class="col-md-1"><input type="number" name="credit_hours" class="form-control" placeholder="Credits" required></div>
                <div class="col-md-2"><input type="text" name="department" class="form-control" placeholder="Department"></div>
                <div class="col-md-1"><input type="number" name="max_capacity" class="form-control" placeholder="Capacity" value="50"></div>
                <div class="col-md-2">
                    <select name="prerequisite_course_id" class="form-select">
                        <option value="">No prerequisite</option>
                        <?php 
                        $allCourses = $pdo->query("SELECT course_id, course_code, course_name FROM courses");
                        foreach ($allCourses as $c): ?>
                            <option value="<?= $c['course_id'] ?>"><?= $c['course_code'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1"><button type="submit" class="btn btn-success">Add</button></div>
            </form>
        </div>
    </div>

    <table class="table table-bordered">
        <thead><tr><th>Code</th><th>Name</th><th>Credits</th><th>Department</th><th>Capacity</th><th>Prerequisite</th></tr></thead>
        <tbody>
        <?php foreach ($courses as $c): ?>
            <tr>
                <td><?= $c['course_code'] ?></td>
                <td><?= $c['course_name'] ?></td>
                <td><?= $c['credit_hours'] ?></td>
                <td><?= $c['department'] ?></td>
                <td><?= $c['max_capacity'] ?></td>
                <td><?= $c['prereq_code'] ?? 'None' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body></html>