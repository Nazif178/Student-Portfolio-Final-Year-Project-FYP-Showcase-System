<?php
session_start();
require "db.php";
require "functions.php";

if (!empty($_SESSION["user_id"])) {
    header("Location: " . (($_SESSION["role"] ?? "") === "Admin" ? "admin_dashboard.php" : "student_dashboard.php"));
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $error = "Please fill in every field.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";
    } else {
        // Public registration always creates a Student account.
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'Student')");

        if (!$stmt) {
            $error = "Could not prepare registration. Check your database tables.";
        } else {
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {
                $success = "Registration successful. You can now log in.";
            } elseif ($stmt->errno === 1062) {
                $error = "That email address is already registered.";
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
    }
}
include "header.php";
?>
<div class="form-card">
    <h2>Create Student Account</h2>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert success"><?= e($success) ?> <a href="login.php">Log in</a></div><?php endif; ?>
    <form method="post" action="register.php">
        <label for="full_name">Full name</label>
        <input id="full_name" name="full_name" maxlength="100" required value="<?= e($_POST["full_name"] ?? "") ?>">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" maxlength="150" required value="<?= e($_POST["email"] ?? "") ?>">

        <label for="password">Password (at least 8 characters)</label>
        <input id="password" type="password" name="password" minlength="8" required>

        <button type="submit">Register</button>
    </form>
    <p>Already registered? <a href="login.php">Log in here</a>.</p>
</div>
<?php include "footer.php"; ?>
