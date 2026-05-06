<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
    <!-- Bootstrap 5 CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Custom Colors */
        :root {
            --primary-color: #1a3c6e;   /* Deep blue */
            --secondary-color: #f8f9fa;
        }
    html, body {
            height: 100%;
            margin: 0;
    }
        body {
            padding-top: 56px; /* navbar height */
        }
        .sidebar .nav-link {
            color: #93eb3c;
            transition: 0.2s;
        }
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            background: var(--primary-color);
            color: white !important;
        }
        /* Mobile adjustments */
        @media (max-width: 991.98px) {
            .sidebar {
                display: none;
            }
        }
        /* Main content offset for desktop sidebar */
        @media (min-width: 992px) {
            body {
                margin-left: 250px;
            }
        }

        @media (min-width: 992px) {
        body {
            margin-left: 250px;
        }
        #desktopSidebar {
            width: 250px;
            position: fixed;
            left: 0;
            top: 56px;
            height: calc(100vh - 56px);
            overflow-y: auto;
            z-index: 1000;
        }
    }
    main {
        min-height: calc(100vh - 56px);
        width: 100%;
    }
    </style>
</head>
<body>
<?php include 'topbar.php'; ?>
<?php include 'sidebar.php'; ?>
<main class="mt-3 px-3 px-lg-4 pb-5">