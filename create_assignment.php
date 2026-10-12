create_assignment.php

<?php
session_start();
require "db.php";
require "functions.php";
require_role("Admin");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($title === "" || $description === "") {
        $error = "Please enter both the title and description.";
    } else {
        $stmt = $conn->prepare("INSERT INTO assignments (title, description, created_by) VALUES (?, ?, ?)");
        if ($stmt) {
            $adminId = (int)$_SESSION["user_id"];
            $stmt->bind_param("ssi", $title, $description, $adminId);
            if ($stmt->execute()) {
                $success = "Assignment created successfully.";
                $_POST = []; // clear the form after a successful save
            } else {
                $error = "Could not create assignment.";
            }
            $stmt->close();
        } else {
            $error = "Could not prepare the database query.";
        }
    }
}
$pageTitle = "Create Assignment";
include "header.php";
?>
<div class="form-card">
    <p>
        <a class="button-link" href="admin_dashboard.php">Back to Admin Dashboard</a>
        <a class="button-link" href="view_submissions.php">View Submissions</a>
    </p>
    <h2>Create Assignment</h2>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
    <form method="post" action="create_assignment.php">
        <label for="title">Assignment title</label>
        <input id="title" name="title" maxlength="150" required value="<?= e($_POST["title"] ?? "") ?>">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5" required><?= e($_POST["description"] ?? "") ?></textarea>
        <button type="submit">Create Assignment</button>
    </form>
</div>
<?php include "footer.php"; ?>
