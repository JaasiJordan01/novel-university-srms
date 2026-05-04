<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: #58c505;">
  <div class="container-fluid">
    <!-- Sidebar toggler for mobile -->
    <button class="btn btn-outline-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
      ☰
    </button>

    <a class="navbar-brand fw-bold" href="../dashboard.php">
      🎓 Novel International University
    </a>

    <div class="ms-auto d-flex align-items-center">
      <span class="text-light me-3 d-none d-md-block">
        <?= htmlspecialchars($_SESSION['full_name']) ?> (<?= $_SESSION['role'] ?>)
      </span>
      <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>