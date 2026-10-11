<?php

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'assignment_system';

$conn = new mysqli($hostname, $username, $password, $database) or die('Connection to MYSQL failed');

echo 'database connected';
?>