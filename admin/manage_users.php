<?php
require_once '../includes/auth_check.php';
check_role(['admin']);
require_once '../config/database.php';
$message = '';

// Process form submission for adding a user
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
        $message = "<div class='alert alert-success alert-dismissible fade show'>User created successfully.
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger alert-dismissible fade show'>Error: " . $e->getMessage() . "
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
    }
}

// Fetch all users for the table
$users = $pdo->query("SELECT * FROM users ORDER BY role, full_name")->fetchAll();

require_once '../includes/header.php';
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Payment</li>
  </ol>
</nav>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>User Management</h2>
    <!-- Button to trigger the modal -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus"></i> Add New User
    </button>
</div>

<?= $message ?>

<div class="card shadow-sm">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['user_id'] ?></td>
                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge bg-primary"><?= $u['role'] ?></span></td>
                    <td><?= htmlspecialchars($u['department']) ?></td>
                    <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-secondary" disabled><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" disabled><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== MODAL ==================== -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addUserModalLabel">
            <i class="bi bi-person-plus"></i> Add New User
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Username*</label>
              <input type="text" name="username" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Password*</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Role*</label>
              <select name="role" class="form-select" required>
                <option value="">-- Select Role --</option>
                <option value="admin">Admin</option>
                <option value="registry">Registry</option>
                <option value="finance">Finance</option>
                <option value="lecturer">Lecturer</option>
                <option value="student">Student</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Full Name*</label>
              <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <input type="text" name="department" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>