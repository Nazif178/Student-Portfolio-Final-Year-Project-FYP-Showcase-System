<?php
session_start();
require "db.php";
require "functions.php";
require_role("Admin");

$submissionId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$submissionId || $submissionId < 1) {
    http_response_code(400);
    exit("Invalid submission ID.");
}

$stmt = $conn->prepare("SELECT file_name, file_path FROM submissions WHERE submission_id = ?");
$stmt->bind_param("i", $submissionId);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$submission) {
    http_response_code(404);
    exit("Submission not found.");
}

$uploadRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . "uploads");
$filePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . $submission["file_path"]);

if (!$uploadRoot || !$filePath || strpos($filePath, $uploadRoot . DIRECTORY_SEPARATOR) !== 0 || !is_file($filePath)) {
    http_response_code(404);
    exit("File not found.");
}

$downloadName = str_replace(["\r", "\n", '"'], "", basename($submission["file_name"]));
header("Content-Type: application/octet-stream");
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header("Content-Length: " . filesize($filePath));
readfile($filePath);
exit();
?>
