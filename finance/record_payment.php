<?php
require_once '../includes/auth_check.php';
check_role(['admin','finance']);
require_once '../config/database.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $amount     = $_POST['amount'];
    $method     = $_POST['payment_method'];
    $semester   = $_POST['semester'];
    $academic_year = $_POST['academic_year'];
    $received_by = $_SESSION['user_id'];

    // check if student exists
    $chk = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $chk->execute([$student_id]);
    if (!$chk->fetch()) {
        $message = "<div class='alert alert-danger'>Student ID not found.</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO payments (student_id, amount, payment_date, payment_method, semester, academic_year, received_by)
                               VALUES (?, ?, CURDATE(), ?, ?, ?, ?)");
        $stmt->execute([$student_id, $amount, $method, $semester, $academic_year, $received_by]);
        $message = "<div class='alert alert-success'>Payment of $amount recorded successfully.</div>";
    }
}
?>
<!DOCTYPE html>
<html><head><title>Record Payment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-4">
    <h3>Record Tuition Payment</h3>
    <?= $message ?>
    <form method="post">
        <div class="row">
            <div class="col-md-3"><label>Student ID</label><input type="text" name="student_id" class="form-control" required></div>
            <div class="col-md-3"><label>Amount</label><input type="number" name="amount" class="form-control" step="0.01" required></div>
            <div class="col-md-3"><label>Payment Method</label><input type="text" name="payment_method" class="form-control" placeholder="e.g., Bank, Mobile Money" required></div>
            <div class="col-md-3">
                <label>Semester</label>
                <select name="semester" class="form-select" required>
                    <option value="Sem1">Semester 1</option>
                    <option value="Sem2">Semester 2</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-3"><label>Academic Year</label><input type="text" name="academic_year" class="form-control" placeholder="2026/2027" required></div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit Payment</button>
        <a href="../dashboard.php" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
</body></html>