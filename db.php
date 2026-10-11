<?php

$conn = mysqli_connect("localhost", "root", "", "assignment_system");

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}
?>