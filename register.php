<?php
include 'includes/config.php';
include 'includes/auth.php';

$message = '';
$error = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $auth = new Auth($pdo);
    
    if ($auth->isLoggedIn()) {
        header("Location: index.php");
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        
        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            $error = "All fields are required.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters long.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (strlen($username) < 3) {
            $error = "Username must be at least 3 characters long.";
        } else {
            $result = $auth->register($username, $email, $password, $full_name);
            if ($result['success']) {
                $message = $result['message'] . " You can now log in.";
                header("refresh:3;url=login.php");
            } else {
                $error = $result['message'];
            }
        }
    }
    
} catch (PDOException $e) {
    $error = "Connection error: " . $e->getMessage();
}

include './templates/register.html.php';
?>