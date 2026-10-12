<?php
// Escape text before displaying it inside HTML.
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Redirect a visitor who is not logged in.
function require_login() {
    if (empty($_SESSION["user_id"])) {
        header("Location: login.php");
        exit();
    }
}

// Allow only the requested role(s) to access a page.
function require_role($roles) {
    require_login();

    if (!in_array($_SESSION["role"] ?? "", (array)$roles, true)) {
        http_response_code(403);
        exit("Access denied. You do not have permission to view this page.");
    }
}

// Make sure the default admin account exists (admin@admin.com / 1234).
// Works even if the database was imported before the admin row was added.
function ensure_default_admin($conn) {
    $email = "admin@admin.com";
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
    if (!$check) { return; }
    $check->bind_param("s", $email);
    $check->execute();
    $exists = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$exists) {
        $name = "Administrator";
        $hash = password_hash("1234", PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'Admin')");
        if ($insert) {
            $insert->bind_param("sss", $name, $email, $hash);
            $insert->execute();
            $insert->close();
        }
    }
}
?>
