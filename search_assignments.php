<?php
session_start();
require "db.php";
require "functions.php";

if (empty($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "Student") {
    http_response_code(403);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "Access denied"]);
    exit();
}

$search = trim($_GET["q"] ?? "");
$keyword = "%" . $search . "%";

$stmt = $conn->prepare(
    "SELECT assignment_id, title, description
     FROM assignments
     WHERE title LIKE ? OR description LIKE ?
     ORDER BY assignment_id DESC"
);

if (!$stmt) {
    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "Search query failed"]);
    exit();
}

$stmt->bind_param("ss", $keyword, $keyword);
$stmt->execute();
$result = $stmt->get_result();
$assignments = [];

while ($row = $result->fetch_assoc()) {
    $assignments[] = $row;
}

header("Content-Type: application/json; charset=utf-8");
echo json_encode($assignments);
$stmt->close();
?>
