<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Novel International University – Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3c6e;
            --primary-light: #2a5fa8;
            --secondary: #f8f9fa;
        }

        body {
            background: linear-gradient(135deg, #0d1b3e 0%, #1a3c6e 60%, #2a5fa8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .login-card {
            max-width: 480px;
            width: 100%;
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            background-color: #ffffff;
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .login-header {
            background: linear-gradient(to right, #1a3c6e, #2a5fa8);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .login-header .university-logo {
            font-size: 2.5rem;
            margin-bottom: 5px;
        }

        .login-header h2 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        .login-body {
            padding: 30px 30px 20px;
        }

        .form-floating {
            margin-bottom: 20px;
        }

        .form-floating input {
            border-radius: 10px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-floating input:focus {
            border-color: #1a3c6e;
            box-shadow: 0 0 0 0.2rem rgba(26, 60, 110, 0.25);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 10px;
            background: linear-gradient(to right, #1a3c6e, #2a5fa8);
            border: none;
            transition: background 0.3s, transform 0.2s;
        }

        .btn-login:hover {
            background: linear-gradient(to right, #142e56, #1f4980);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 60, 110, 0.4);
        }

        .error-alert {
            border-left: 4px solid #dc3545;
            background-color: #fff5f5;
            color: #842029;
            font-weight: 500;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 20px;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-footer {
            text-align: center;
            padding: 15px;
            background-color: var(--secondary);
            font-size: 0.85rem;
            color: #6c757d;
        }

        .login-footer a {
            color: #1a3c6e;
            text-decoration: none;
            font-weight: 500;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="university-logo"><i class="bi bi-mortarboard-fill"></i></div>
        <h2>Novel International University</h2>
        <p>Student Records Management System</p>
    </div>

    <div class="login-body">
        <?php if (isset($_GET['error'])): ?>
            <div class="error-alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="auth/login_process.php" method="post">
            <div class="form-floating">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                <label for="username"><i class="bi bi-person-fill me-1"></i>Username</label>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                <label for="password"><i class="bi bi-lock-fill me-1"></i>Password</label>
            </div>

            <button type="submit" class="btn btn-primary btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                First time? The administrator creates all accounts.
            </small>
        </div>
    </div>

    <div class="login-footer">
        &copy; <?= date('Y') ?> Novel International University. All rights reserved.
    </div>
</div>

</body>
</html>