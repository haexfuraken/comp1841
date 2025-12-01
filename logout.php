<?php
include 'includes/config.php';
include 'includes/auth.php';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $auth = new Auth($pdo);
    
    $result = $auth->logout();
    
    header("Location: index.php?msg=logout_success");
    exit;
    
} catch (Exception $e) {
    header("Location: index.php?error=logout_failed");
    exit;
}
?>