<?php
session_start();
require "db.php";
require "functions.php";
require_role("Admin");

$pageTitle = "Admin Dashboard";
include "header.php";

$assignmentCount = 0;
$submissionCount = 0;
$userCount = 0;

$result = $conn->query("SELECT COUNT(*) AS total FROM assignments");
if ($result) { $assignmentCount = (int)$result->fetch_assoc()["total"]; }
$result = $conn->query("SELECT COUNT(*) AS total FROM submissions");
if ($result) { $submissionCount = (int)$result->fetch_assoc()["total"]; }
$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'Student'");
if ($result) { $userCount = (int)$result->fetch_assoc()["total"]; }
?>
<h1>Admin Dashboard</h1>
<p>Welcome, <?= e($_SESSION["full_name"]) ?>. Manage assignments and student submissions here.</p>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="content-card"><h5>Student Accounts</h5><h2><?= $userCount ?></h2></div></div>
    <div class="col-md-4"><div class="content-card"><h5>Assignments</h5><h2><?= $assignmentCount ?></h2></div></div>
    <div class="col-md-4"><div class="content-card"><h5>Submissions</h5><h2><?= $submissionCount ?></h2></div></div>
</div>
<div class="content-card">
    <h3>Admin Actions</h3>
    <p><a class="button-link" href="create_assignment.php">Create Assignment</a></p>
    <p><a class="button-link" href="view_submissions.php">View Student Submissions</a></p>
</div>
<?php include "footer.php"; ?>
