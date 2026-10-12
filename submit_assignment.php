<?php
session_start();
require "db.php";
require "functions.php";
require_role("Student");

$assignmentId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $assignmentId = filter_input(INPUT_POST, "assignment_id", FILTER_VALIDATE_INT);
}

// No assignment chosen yet (e.g. opened from the menu): show a list to pick from.
if ($_SERVER["REQUEST_METHOD"] !== "POST" && !isset($_GET["id"])) {
    $list = $conn->query("SELECT assignment_id, title, description FROM assignments ORDER BY assignment_id DESC");
    $pageTitle = "Submit Assignment";
    include "header.php";
    ?>
    <h1>Submit Assignment</h1>
    <p>
        <a class="button-link" href="student_dashboard.php">Back to Dashboard</a>
        <a class="button-link" href="my_submissions.php">View My Submissions</a>
    </p>
    <div class="content-card">
        <h3>Choose an assignment</h3>
        <?php if (!$list || $list->num_rows === 0): ?>
            <p>No assignments have been created yet.</p>
        <?php else: ?>
            <?php while ($row = $list->fetch_assoc()): ?>
                <div class="assignment-card">
                    <h4><?= e($row["title"]) ?></h4>
                    <p><?= e($row["description"]) ?></p>
                    <a class="button-link" href="submit_assignment.php?id=<?= (int)$row["assignment_id"] ?>">Submit for this assignment</a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <?php
    include "footer.php";
    exit();
}

if (!$assignmentId || $assignmentId < 1) {
    http_response_code(400);
    exit("Invalid assignment ID.");
}

$stmt = $conn->prepare("SELECT assignment_id, title, description FROM assignments WHERE assignment_id = ?");
$stmt->bind_param("i", $assignmentId);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$assignment) {
    http_response_code(404);
    exit("Assignment not found.");
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_FILES["assignment_file"]) || $_FILES["assignment_file"]["error"] !== UPLOAD_ERR_OK) {
        $error = "Please choose a file to upload. Maximum size is 5 MB.";
    } else {
        $file = $_FILES["assignment_file"];
        $maxSize = 5 * 1024 * 1024;
        $originalName = basename($file["name"]);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ["pdf", "docx", "txt"];

        if ($file["size"] > $maxSize) {
            $error = "File is too large. Maximum size is 5 MB.";
        } elseif (!in_array($extension, $allowedExtensions, true)) {
            $error = "Only PDF, DOCX, and TXT files are allowed.";
        } else {
            // Check actual MIME type as well as the filename extension.
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file["tmp_name"]);
            $allowedMimes = [
                "pdf" => ["application/pdf"],
                "docx" => ["application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/zip", "application/octet-stream"],
                "txt" => ["text/plain", "application/octet-stream"]
            ];

            if (!in_array($mime, $allowedMimes[$extension], true)) {
                $error = "The file content does not appear to match its extension.";
            } else {
                $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $storedName = bin2hex(random_bytes(16)) . "." . $extension;
                $absolutePath = $uploadDir . DIRECTORY_SEPARATOR . $storedName;
                $relativePath = "uploads/" . $storedName;

                if (move_uploaded_file($file["tmp_name"], $absolutePath)) {
                    $userId = (int)$_SESSION["user_id"];
                    $insert = $conn->prepare("INSERT INTO submissions (user_id, assignment_id, file_name, file_path) VALUES (?, ?, ?, ?)");
                    if ($insert) {
                        $insert->bind_param("iiss", $userId, $assignmentId, $originalName, $relativePath);
                        if ($insert->execute()) {
                            $success = "Your assignment was submitted successfully.";
                        } else {
                            @unlink($absolutePath);
                            $error = "Could not save your submission in the database.";
                        }
                        $insert->close();
                    } else {
                        @unlink($absolutePath);
                        $error = "Could not prepare the submission query.";
                    }
                } else {
                    $error = "Upload failed. Check that the uploads folder is writable.";
                }
            }
        }
    }
}
$pageTitle = "Submit Assignment";
include "header.php";
?>
<div class="content-card">
    <p>
        <a class="button-link" href="student_dashboard.php">Back to Dashboard</a>
        <a class="button-link" href="submit_assignment.php">All Assignments</a>
        <a class="button-link" href="my_submissions.php">My Submissions</a>
    </p>
    <h2><?= e($assignment["title"]) ?></h2>
    <p><?= nl2br(e($assignment["description"])) ?></p>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?= e($success) ?> <a href="my_submissions.php">View my submissions</a></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="submit_assignment.php?id=<?= (int)$assignmentId ?>">
        <input type="hidden" name="assignment_id" value="<?= (int)$assignmentId ?>">
        <label for="assignment_file">Choose file (PDF, DOCX, TXT; max 5 MB)</label>
        <input id="assignment_file" type="file" name="assignment_file" accept=".pdf,.docx,.txt" required>
        <button type="submit">Upload Submission</button>
    </form>
</div>
<?php include "footer.php"; ?>
