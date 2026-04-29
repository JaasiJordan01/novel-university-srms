<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        // Optionally store student_id if role is student
        if ($user['role'] == 'student') {
            // Get student_id from students table where user_id = $_SESSION['user_id']
            $stmt2 = $pdo->prepare("SELECT student_id FROM students WHERE user_id = ?");
            $stmt2->execute([$user['user_id']]);
            $student = $stmt2->fetch();
            if ($student) {
                $_SESSION['student_id'] = $student['student_id'];
            }
        }
        header("Location: ../dashboard.php");
        exit();
    } else {
        header("Location: ../index.php?error=Invalid username or password");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}