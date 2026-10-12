<?php
session_start();
require "db.php";
require "functions.php";
require_role("Student");

$userId = (int)$_SESSION["user_id"];
$stmt = $conn->prepare(
    "SELECT s.submission_id, a.title, s.file_name, s.submitted_at
     FROM submissions s
     INNER JOIN assignments a ON s.assignment_id = a.assignment_id
     WHERE s.user_id = ?
     ORDER BY s.submitted_at DESC"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$pageTitle = "My Submissions";
include "header.php";
?>
<h1>My Submissions</h1>
<p>
    <a class="button-link" href="student_dashboard.php">Back to Dashboard</a>
    <a class="button-link" href="submit_assignment.php">Submit Assignment</a>
</p>
<div class="content-card table-wrap">
    <table class="table table-striped">
        <thead><tr><th>Assignment</th><th>File</th><th>Submitted at</th></tr></thead>
        <tbody>
        <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="3">You have not submitted any assignments yet.</td></tr>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= e($row["title"]) ?></td>
                    <td><?= e($row["file_name"]) ?></td>
                    <td><?= e($row["submitted_at"]) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php
$stmt->close();
include "footer.php";
?>
