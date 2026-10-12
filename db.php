
<?php
// Connect to the MySQL database
$conn = new mysqli(
    "localhost",
    "root",
    "",
    "assignment_system"
);

// Stop if the connection fails
if ($conn->connect_error) {
    die("Database connection failed");
}

// Use UTF-8 for text
$conn->set_charset("utf8mb4");
?>
