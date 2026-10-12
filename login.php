<?php
session_start();
require "db.php";
require "functions.php";

if (!empty($_SESSION["user_id"])) {
    header("Location: " . (($_SESSION["role"] ?? "") === "Admin" ? "admin_dashboard.php" : "student_dashboard.php"));
    exit();
}

$error = "";

// Creates admin@admin.com / 1234 the first time if it is missing.
ensure_default_admin($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT user_id, full_name, email, password, role FROM users WHERE email = ? LIMIT 1");

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = (int)$user["user_id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];

            header("Location: " . ($user["role"] === "Admin" ? "admin_dashboard.php" : "student_dashboard.php"));
            $stmt->close();
            exit();
        }

        $stmt->close();
    }

    $error = "Invalid email or password.";
}
include "header.php";
?>
<div class="form-card">
    <h2>Login</h2>
    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="login.php">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required value="<?= e($_POST["email"] ?? "") ?>">

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p>New user? <a href="register.php">Register as a Student</a>.</p>
    <p class="small-muted">Admin login: <strong>admin@admin.com</strong> / <strong>1234</strong></p>
</div>
<?php include "footer.php"; ?>
