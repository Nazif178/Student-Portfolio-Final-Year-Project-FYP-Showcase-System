<?php
session_start();
require "db.php";
require "functions.php";
require_role("Admin");

$sql = "SELECT s.submission_id, u.full_name, u.email, a.title, s.file_name, s.submitted_at
        FROM submissions s
        INNER JOIN users u ON s.user_id = u.user_id
        INNER JOIN assignments a ON s.assignment_id = a.assignment_id
        ORDER BY s.submitted_at DESC";
$result = $conn->query($sql);

$pageTitle = "View Submissions";
include "header.php";
?>
<h1>Student Submissions</h1>
<p>
    <a class="button-link" href="admin_dashboard.php">Back to Admin Dashboard</a>
    <a class="button-link" href="create_assignment.php">Create Assignment</a>
</p>
<div class="content-card table-wrap">
    <table class="table table-striped table-hover">
        <thead><tr><th>Student</th><th>Email</th><th>Assignment</th><th>File</th><th>Submitted at</th><th>Download</th></tr></thead>
        <tbody>
        <?php if (!$result || $result->num_rows === 0): ?>
            <tr><td colspan="6">No submissions yet.</td></tr>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= e($row["full_name"]) ?></td>
                    <td><?= e($row["email"]) ?></td>
                    <td><?= e($row["title"]) ?></td>
                    <td><?= e($row["file_name"]) ?></td>
                    <td><?= e($row["submitted_at"]) ?></td>
                    <td><a href="download_file.php?id=<?= (int)$row["submission_id"] ?>">Download</a></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include "footer.php"; ?>
