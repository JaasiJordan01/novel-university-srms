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

    // Check if student exists
    $chk = $pdo->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $chk->execute([$student_id]);
    if (!$chk->fetch()) {
        $message = "<div class='alert alert-danger alert-dismissible fade show'>Student ID not found.
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO payments (student_id, amount, payment_date, payment_method, semester, academic_year, received_by)
                               VALUES (?, ?, CURDATE(), ?, ?, ?, ?)");
        $stmt->execute([$student_id, $amount, $method, $semester, $academic_year, $received_by]);
        $message = "<div class='alert alert-success alert-dismissible fade show'>Payment of " . number_format($amount) . " recorded successfully.
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

require_once '../includes/header.php';
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Payment</li>
  </ol>
</nav>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Record Payment</h2>
    <a href="../finance/fee_reports.php" class="btn btn-outline-primary">
        <i class="bi bi-graph-up"></i> View Fee Reports
    </a>
</div>

<?= $message ?>

<div class="card shadow-sm">
    <div class="card-header bg-light">
        <strong><i class="bi bi-credit-card"></i> New Payment Entry</strong>
    </div>
    <div class="card-body">
        <form method="post">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Student ID*</label>
                    <input type="text" name="student_id" class="form-control" placeholder="e.g., STU20260001" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Amount*</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Method</label>
                    <input type="text" name="payment_method" class="form-control" placeholder="Bank Transfer, Mobile Money, Cash" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Semester*</label>
                    <select name="semester" class="form-select" required>
                        <option value="Sem1">Semester 1</option>
                        <option value="Sem2">Semester 2</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Academic Year*</label>
                    <input type="text" name="academic_year" class="form-control" placeholder="2026/2027" value="2026/2027" required>
                </div>
                <div class="col-md-12 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Submit Payment
                    </button>
                    <a href="../dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>