<?php
require_once 'config/database.php';
$message = '';
$token = $_GET['token'] ?? '';

// Verify token
$stmt = $pdo->prepare("SELECT * FROM applicants WHERE token = ? AND status = 'approved'");
$stmt->execute([$token]);
$applicant = $stmt->fetch();

if (!$applicant) {
    die("Invalid or expired link.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    if ($password !== $confirm) {
        $message = '<div class="alert alert-danger">Passwords do not match.</div>';
    } elseif (strlen($password) < 6) {
        $message = '<div class="alert alert-danger">Password must be at least 6 characters.</div>';
    } else {
        // Hash password
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // Create user account
        $username = strtolower($applicant['email'] ?? 'app'.$applicant['applicant_id']);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, full_name, email) VALUES (?,?,'student',?,?)");
        $fullName = $applicant['first_name'].' '.$applicant['last_name'];
        $stmt->execute([$username, $hash, $fullName, $applicant['email']]);
        $user_id = $pdo->lastInsertId();

        // Generate student ID (e.g., STU + year + seq)
        $year = date('Y');
        $seq = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn() + 1;
        $student_id = 'STU'.$year.str_pad($seq, 4, '0', STR_PAD_LEFT);
        // Insert student record
        $stmt = $pdo->prepare("INSERT INTO students (student_id, user_id, first_name, last_name, email, phone, program, enrollment_year) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$student_id, $user_id, $applicant['first_name'], $applicant['last_name'], $applicant['email'], $applicant['phone'], $applicant['program_applied'], $year]);

        // Update applicant record to link user and clear token
        $stmt = $pdo->prepare("UPDATE applicants SET password_hash = ?, user_id = ?, token = NULL WHERE applicant_id = ?");
        $stmt->execute([$hash, $user_id, $applicant['applicant_id']]);

        $message = '<div class="alert alert-success">Account created! You can now <a href="index.php">login</a> with your email/username and password.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Your Password – Novel International University</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 450px;">
    <div class="card">
        <div class="card-header bg-primary text-white"><h4>Set Your Password</h4></div>
        <div class="card-body">
            <?= $message ?>
            <?php if (!$message || strpos($message, 'alert-success') === false): ?>
            <form method="post">
                <div class="mb-3"><label>New Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="mb-3"><label>Confirm Password</label><input type="password" name="confirm_password" class="form-control" required></div>
                <button type="submit" class="btn btn-primary w-100">Set Password & Activate Account</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>