<?php
require_once '../includes/auth_check.php';
check_role(['admin']);
require_once '../config/database.php';
$message = '';

// Add new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = $_POST['username'];
    $plain_password = $_POST['password'];
    $role = $_POST['role'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $department = $_POST['department'];

    $hash = password_hash($plain_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, full_name, email, department) VALUES (?,?,?,?,?,?)");
    try {
        $stmt->execute([$username, $hash, $role, $full_name, $email, $department]);
        $message = "<div class='alert alert-success'>User created.</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch all users
$users = $pdo->query("SELECT * FROM users ORDER BY role, full_name");
?>
<!DOCTYPE html>
<html><head><title>Manage Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body>
<div class="container mt-4">
    <h3>User Management</h3>
    <?= $message ?>
    <div class="card mb-4">
        <div class="card-header">Add New User</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-3"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
                <div class="col-md-2"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                <div class="col-md-2">
                    <select name="role" class="form-select" required>
                        <option value="">-- Role --</option>
                        <option value="admin">Admin</option>
                        <option value="registry">Registry</option>
                        <option value="finance">Finance</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div class="col-md-2"><input type="text" name="full_name" class="form-control" placeholder="Full Name" required></div>
                <div class="col-md-2"><input type="email" name="email" class="form-control" placeholder="Email"></div>
                <div class="col-md-1"><input type="text" name="department" class="form-control" placeholder="Dept"></div>
                <div class="col-md-1"><button type="submit" class="btn btn-primary w-100">Add</button></div>
            </form>
        </div>
    </div>

    <table class="table table-striped">
        <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Full Name</th><th>Email</th><th>Department</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['user_id'] ?></td>
                <td><?= $u['username'] ?></td>
                <td><?= $u['role'] ?></td>
                <td><?= $u['full_name'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['department'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body></html>