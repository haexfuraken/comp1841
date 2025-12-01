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
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = "Please enter both username and password.";
        } else {
            $result = $auth->login($username, $password);
            if ($result['success']) {
                $message = $result['message'];
                header("Location: index.php");
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
    
} catch (PDOException $e) {
    $error = "Connection error: " . $e->getMessage();
}

include './templates/login.html.php';
?>