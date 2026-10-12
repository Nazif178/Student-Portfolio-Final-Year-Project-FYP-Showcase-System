<?php
// Include this file after session_start() on pages that need a session.
require_once "functions.php";
$pageTitle = $pageTitle ?? "Student Portfolio & FYP Showcase System";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?= !empty($_SESSION["user_id"]) ? (($_SESSION["role"] ?? "") === "Admin" ? "admin_dashboard.php" : "student_dashboard.php") : "login.php" ?>">Student Portfolio & FYP</a>
        <div class="navbar-nav ms-auto">
            <?php if (!empty($_SESSION["user_id"])): ?>
                <?php if (($_SESSION["role"] ?? "") === "Admin"): ?>
                    <a class="nav-link" href="admin_dashboard.php">Admin Home</a>
                    <a class="nav-link" href="create_assignment.php">Create Assignment</a>
                    <a class="nav-link" href="view_submissions.php">View Submissions</a>
                <?php else: ?>
                    <a class="nav-link" href="student_dashboard.php">Dashboard</a>
                    <a class="nav-link" href="submit_assignment.php">Submit Assignment</a>
                    <a class="nav-link" href="my_submissions.php">My Submissions</a>
                <?php endif; ?>
                <span class="navbar-text me-3"><?= e($_SESSION["full_name"] ?? "User") ?> (<?= e($_SESSION["role"] ?? "") ?>)</span>
                <a class="nav-link" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link" href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container page-content">
