<?php
$conn = new mysqli("localhost", "root", "", "student_project_db");

if ($conn->connect_error) {
    die("Database connection failed. ");
}
?>
