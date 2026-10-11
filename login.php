<?php
session_start();
require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the login details
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email == "" || $password == "") {
        $message = "Please enter your email and password.";

    } else {
        // Find the account using its email
        $stmt = $conn->prepare(
            "SELECT user_id, full_name, email, password, role
             FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Get the matching account
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Compare the entered password with the saved hash
            if (password_verify($password, $user["password"])) {

                // Create a new session ID after login
                session_regenerate_id(true);

                // Store account details in the session
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["full_name"] = $user["full_name"];
                $_SESSION["role"] = $user["role"];

                // Redirect according to the account role
                if ($user["role"] == "Admin") {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }

                exit();

            } else {
                $message = "Incorrect email or password.";
            }
        } else {
            $message = "Incorrect email or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Login</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST" onsubmit="return validateLogin()">

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" id="password" required>

    <button type="submit">Login</button>

</form>

<p>New student? <a href="register.php">Register</a></p>

<script>
// Check that both fields contain something
function validateLogin() {
    let password = document.getElementById("password").value;

    if (password.trim() == "") {
        alert("Please enter your password.");
        return false;
    }

    return true;
}
</script>

</body>
</html>
