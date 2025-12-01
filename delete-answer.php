<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$auth = new Auth($pdo);
$isLoggedIn = $auth->isLoggedIn();

if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$answer_id = $_GET['id'] ?? null;
$question_id = $_GET['question_id'] ?? null;

if (!$answer_id || !$question_id) {
    header('Location: index.php');
    exit;
}

if (!$auth->ownsAnswer($answer_id) && !$auth->isStaff()) {
    header('Location: view-question.php?id=' . $question_id);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM answers WHERE answer_id = ?");
    $stmt->execute([$answer_id]);
    
    header('Location: view-question.php?id=' . $question_id . '&msg=answer_deleted');
    exit;
} catch (PDOException $e) {
    header('Location: view-question.php?id=' . $question_id . '&error=delete_failed');
    exit;
}
?>