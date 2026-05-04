<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
// If you need to restrict to specific roles, pass them
function check_role($allowed_roles) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: ../dashboard.php?error=unauthorized");
        exit();
    }
}
?>