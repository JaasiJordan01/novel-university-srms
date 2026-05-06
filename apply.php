<?php
require_once 'config/database.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName  = trim($_POST['last_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $program   = $_POST['program_applied'];

    // Basic validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($program)) {
        $message = '<div class="alert alert-danger">Please fill all required fields.</div>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO applicants (first_name, last_name, email, phone, program_applied) VALUES (?,?,?,?,?)");
        $stmt->execute([$firstName, $lastName, $email, $phone, $program]);
        $applicant_id = $pdo->lastInsertId();
        $message = '<div class="alert alert-success">Application submitted! Your reference number is <strong>APP-'.$applicant_id.'</strong>. You will be notified when your application is approved.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply Now – Novel International University</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0d1b3e, #1a3c6e); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { max-width: 600px; width: 100%; border-radius: 16px; }
        .btn-primary { background: linear-gradient(to right, #1a3c6e, #2a5fa8); border: none; }
        .btn-primary:hover { background: linear-gradient(to right, #142e56, #1f4980); }
    </style>
</head>
<body>
<div class="card shadow">
    <div class="card-header text-white text-center" style="background: linear-gradient(to right, #1a3c6e, #2a5fa8);">
        <h3>Novel International University</h3>
        <p>Online Application Form</p>
    </div>
    <div class="card-body">
        <?= $message ?>
        <form method="post">
            <div class="row">
                <div class="col-md-6 mb-3"><label>First Name *</label><input type="text" name="first_name" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label>Last Name *</label><input type="text" name="last_name" class="form-control" required></div>
            </div>
            <div class="mb-3"><label>Email Address *</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
            <div class="mb-3"><label>Program Applied *</label><input type="text" name="program_applied" class="form-control" list="programs" required>
                <datalist id="programs">
                    <option>Bachelor in Software Engineering</option>
                    <option>Diploma in Information Technology</option>
                    <option>Masters in Cyber Security and Networking</option>
                    <option>Bachelors of Science in Agriculture</option>
                    <option>Bachelors in Agrobusiness Management</option>
                    <option>Bachelor in Data Science and AI</option>
                    <option>Certificate in Linguistics</option>
                </datalist>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Application</button>
        </form>
        <div class="mt-3 text-center"><a href="index.php" class="text-decoration-none">Back to Login</a></div>
    </div>
</div>
</body>
</html>