<?php
$role = $_SESSION['role'];
?>

<ul class="nav flex-column p-3">
  <li class="nav-item">
    <a class="nav-link text-white <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active bg-primary' : '' ?>" href="../dashboard.php">
      <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>
  </li>

  <?php if ($role == 'admin' || $role == 'registry'): ?>
  <li class="nav-item">
    <a class="nav-link text-white" data-bs-toggle="collapse" href="#studentsMenu" role="button">
      <i class="bi bi-people-fill me-2"></i> Students
    </a>
    <div class="collapse" id="studentsMenu">
      <a class="nav-link text-white ps-4" href="../registry/register_student.php"><i class="bi bi-person-plus me-1"></i> Register Student</a>
      <a class="nav-link text-white ps-4" href="../registry/admissions.php"><i class="bi bi-list-ul me-1"></i> Admissions</a>
    </div>
  </li>
  <?php endif; ?>

  <?php if ($role == 'admin' || $role == 'registry'): ?>
  <li class="nav-item">
    <a class="nav-link text-white" href="../registry/manage_courses.php">
      <i class="bi bi-book me-2"></i> Courses
    </a>
  </li>
  <?php endif; ?>

  <?php if ($role == 'admin' || $role == 'lecturer'): ?>
  <li class="nav-item">
    <a class="nav-link text-white" href="#">
      <i class="bi bi-file-earmark-text me-2"></i> Results
    </a>
  </li>
  <?php endif; ?>

  <?php if ($role == 'admin' || $role == 'finance'): ?>
  <li class="nav-item">
    <a class="nav-link text-white" data-bs-toggle="collapse" href="#financeMenu" role="button">
      <i class="bi bi-cash-coin me-2"></i> Finance
    </a>
    <div class="collapse" id="financeMenu">
      <a class="nav-link text-white ps-4" href="../finance/record_payment.php"><i class="bi bi-credit-card me-1"></i> Record Payment</a>
      <a class="nav-link text-white ps-4" href="../finance/fee_reports.php"><i class="bi bi-graph-up me-1"></i> Fee Reports</a>
    </div>
  </li>
  <?php endif; ?>

  <li>
    <a class="nav-link text-white" href="../admin/approve_applications.php">
    <i class="bi bi-check-circle me-2"></i> Applications
</a>
  </li>
  <li class="nav-item">
    <a class="nav-link text-white" href="#">
      <i class="bi bi-moon-stars me-2"></i> Dark Mode
    </a>
  </li>
  <li class="nav-item mt-4">
    <a class="nav-link text-white" href="../auth/logout.php">
      <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
  </li>
</ul>