<?php
session_start();
require "db.php";

$message = "";

// Run when the registration form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the form values
    $name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check for empty fields
    if ($name == "" || $email == "" || $password == "") {
        $message = "Please fill in all fields.";

    // Check email format
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email.";

    // Require at least 8 characters
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";

    } else {
        // Hash the password before saving
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement
        $stmt = $conn->prepare(
            "INSERT INTO users (full_name, email, password, role)
             VALUES (?, ?, ?, 'Student')"
        );

        // Attach values to the SQL placeholders
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        // Save the account
        if ($stmt->execute()) {
            $message = "Registration successful. Please login.";
        } else {
            // Error code 1062 means duplicate email
            if ($stmt->errno == 1062) {
                $message = "Email already registered.";
            } else {
                $message = "Registration failed. Please try again.";
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Student Registration</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST" onsubmit="return validateForm()">

    <label>Full Name</label>
    <input type="text" name="full_name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password (minimum 8 characters)</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Register</button>

</form>

<p>Already registered? <a href="login.php">Login</a></p>

<script>
// Simple client-side password validation
function validateForm() {
    let password = document.getElementById("password").value;

    if (password.length < 8) {
        alert("Password must be at least 8 characters.");
        return false;
    }

    return true;
}
</script>

</body>
</html>
