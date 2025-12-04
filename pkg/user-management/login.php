<?php
// START SESSION
session_start();

// INCLUDE DB CONNECTION
require_once "../config.php";

// VARIABLE FOR ERROR MESSAGE
$error = "";

// CHECK FORM SUBMIT
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // PREPARE SQL QUERY
    $query = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    $row    = $result->fetch_assoc();

    // VALIDATE LOGIN
    if ($row && password_verify($password, $row["password"])) {

        // SET SESSION VARIABLES
        $_SESSION["loggedin"] = true;
        $_SESSION["id"]       = $row["id"];
        $_SESSION["name"]     = $row["name"];
        $_SESSION["role"]     = $row["role"];

        // REDIRECT BASED ON ROLE
        if ($row["role"] === "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit;

    } else {
        $error = "Invalid email or password!";
    }

    $query->close();
}
?>
