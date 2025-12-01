<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$auth = new Auth($pdo);
$auth->requireLogin();

$question_id = $_GET['id'] ?? null;

if (!$question_id) {
    header('Location: index.php');
    exit;
}

// Check if user owns this question or is staff
if (!$auth->ownsQuestion($question_id) && !$auth->isStaff()) {
    header('Location: view-question.php?id=' . $question_id . '&error=not_authorized');
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Delete related votes first
    $stmt = $pdo->prepare("DELETE FROM votes WHERE question_id = ?");
    $stmt->execute([$question_id]);
    
    // Delete related answer votes
    $stmt = $pdo->prepare("DELETE v FROM votes v INNER JOIN answers a ON v.answer_id = a.answer_id WHERE a.question_id = ?");
    $stmt->execute([$question_id]);
    
    // Delete related answers
    $stmt = $pdo->prepare("DELETE FROM answers WHERE question_id = ?");
    $stmt->execute([$question_id]);
    
    // Delete the question
    $stmt = $pdo->prepare("DELETE FROM questions WHERE question_id = ?");
    $stmt->execute([$question_id]);
    
    $pdo->commit();
    header('Location: index.php?success=question_deleted');
    exit;
} catch (PDOException $e) {
    $pdo->rollback();
    header('Location: view-question.php?id=' . $question_id . '&error=delete_failed');
    exit;
}
?>