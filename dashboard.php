<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - USRMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">USRMS</a>
    <span class="navbar-text text-light">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></span>
    <a href="auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>
<?php include 'includes/board_notice.php'; ?>

<div class="container mt-5">
    <div class="row">
        <?php if ($role == 'admin'): ?>
            <div class="col-md-3"><a href="admin/manage_users.php" class="btn btn-primary w-100 p-3">Manage Users</a></div>
        <?php endif; ?>
        <?php if ($role == 'registry' || $role == 'admin'): ?>
            <div class="col-md-3"><a href="registry/register_student.php" class="btn btn-success w-100 p-3">Register Student</a></div>
            <div class="col-md-3"><a href="registry/manage_courses.php" class="btn btn-info w-100 p-3">Manage Courses</a></div>
        <?php endif; ?>
        <?php if ($role == 'student'): ?>
            <div class="col-md-3"><a href="student/enroll.php" class="btn btn-primary w-100 p-3">Course Registration</a></div>
            <div class="col-md-3"><a href="student/view_results.php" class="btn btn-success w-100 p-3">My Results</a></div>
            <div class="col-md-3"><a href="student/fee_balance.php" class="btn btn-info w-100 p-3">Fee Balance</a></div>
        <?php endif; ?>
        <?php if ($role == 'finance'): ?>
            <div class="col-md-3"><a href="finance/record_payment.php" class="btn btn-warning w-100 p-3">Record Payment</a></div>
            <div class="col-md-3"><a href="finance/fee_reports.php" class="btn btn-secondary w-100 p-3">Fee Reports</a></div>
        <?php endif; ?>
        <?php if ($role == 'lecturer'): ?>
            <div class="col-md-3"><a href="lecturer/enter_marks.php" class="btn btn-dark w-100 p-3">Enter Marks</a></div>
            <div class="col-md-3"><a href="lecturer/class_lists.php" class="btn btn-outline-dark w-100 p-3">Class Lists</a></div>
        <?php endif; ?>
        <?php
require_once 'config/database.php';
$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_courses  = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$total_revenue  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments")->fetchColumn();
?>
<?php if (in_array($role, ['admin','registry','finance','lecturer'])): ?>
<div class="row mt-5">
    <div class="col-md-4"><div class="card text-center"><div class="card-body"><h5>Total Students</h5><p class="display-6"><?= $total_students ?></p></div></div></div>
    <div class="col-md-4"><div class="card text-center"><div class="card-body"><h5>Courses</h5><p class="display-6"><?= $total_courses ?></p></div></div></div>
    <div class="col-md-4"><div class="card text-center"><div class="card-body"><h5>Total Payments</h5><p class="display-6"><?= number_format($total_revenue) ?></p></div></div></div>
</div>
<?php endif; ?>
    </div>
</div>
</body>
</html>