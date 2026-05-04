<?php
require_once '../includes/auth_check.php';
check_role(['admin','registry']);

require_once '../config/database.php';
$message = '';

// Generate student ID on page load
$year = date('Y');
$stmt = $pdo->query("SELECT MAX(student_id) AS last_id FROM students WHERE student_id LIKE 'STU$year%'");
$row = $stmt->fetch();
$seq = 1;
if ($row['last_id']) {
    $seq = (int)substr($row['last_id'], -4) + 1;
}
$new_student_id = 'STU' . $year . str_pad($seq, 4, '0', STR_PAD_LEFT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $dob        = $_POST['dob'];
    $gender     = $_POST['gender'];
    $address    = $_POST['address'];
    $phone      = $_POST['phone'];
    $email      = $_POST['email'];
    $program    = $_POST['program'];
    $enrollment_year = $_POST['enrollment_year'];
    $create_account  = isset($_POST['create_account']);

    // Insert student
    $stmt = $pdo->prepare("INSERT INTO students (student_id, first_name, last_name, dob, gender, address, phone, email, program, enrollment_year) VALUES (?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$student_id, $first_name, $last_name, $dob, $gender, $address, $phone, $email, $program, $enrollment_year]);

    // Optionally create a user account
    if ($create_account) {
        $default_password = 'password123';
        $hashed = password_hash($default_password, PASSWORD_DEFAULT);
        // Generate a username: first part of email or STU+studentID
        $username = strtolower($email ?: $student_id);
        $stmt2 = $pdo->prepare("INSERT INTO users (username, password_hash, role, full_name, email) VALUES (?,?, 'student', ?, ?)");
        $stmt2->execute([$username, $hashed, "$first_name $last_name", $email]);
        $user_id = $pdo->lastInsertId();
        // Link student to user
        $stmt3 = $pdo->prepare("UPDATE students SET user_id = ? WHERE student_id = ?");
        $stmt3->execute([$user_id, $student_id]);
    }
    $message = "<div class='alert alert-success'>Student registered successfully. ID: $student_id</div>";
}
?>
<!DOCTYPE html>
<html><head><title>Register Student</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-4">
    <h3>Register New Student</h3>
    <?= $message ?>
    <form method="post">
        <div class="row">
            <div class="col-md-3">
                <label>Student ID</label>
                <input type="text" name="student_id" class="form-control" value="<?= $new_student_id ?>" readonly>
            </div>
            <div class="col-md-3"><label>First Name</label><input type="text" name="first_name" class="form-control" required></div>
            <div class="col-md-3"><label>Last Name</label><input type="text" name="last_name" class="form-control" required></div>
            <div class="col-md-3"><label>Date of Birth</label><input type="date" name="dob" class="form-control"></div>
        </div>
        <div class="row mt-3">
            <div class="col-md-3"><label>Gender</label><select name="gender" class="form-select"><option value="M">Male</option><option value="F">Female</option></select></div>
            <div class="col-md-3"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
            <div class="col-md-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
            <div class="col-md-3"><label>Program</label><input type="text" name="program" class="form-control" required></div>
        </div>
        <div class="row mt-3">
            <div class="col-md-3"><label>Enrollment Year</label><input type="number" name="enrollment_year" class="form-control" value="<?= date('Y') ?>"></div>
            <div class="col-md-3"><label>Address</label><textarea name="address" class="form-control"></textarea></div>
            <div class="col-md-3 mt-4">
                <input type="checkbox" name="create_account" checked> Create Student Login
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Register</button>
    </form>
</div>
<?php require_once '../includes/footer.php'; ?>
</body></html>