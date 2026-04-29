<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Novel International University – SRMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optional: custom CSS -->
    <style>
        body { padding-top: 70px; } /* to avoid navbar overlapping */
        .navbar-brand img { height: 30px; margin-right: 10px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="../dashboard.php">
        🎓 Novel International University
    </a>
    <span class="navbar-text text-light me-3">
        <?= htmlspecialchars($_SESSION['full_name']) ?> (<?= $_SESSION['role'] ?>)
    </span>
    <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
  </div>
</nav>
<div class="container mt-4">