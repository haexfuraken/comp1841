<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$auth = new Auth($pdo);
$auth->requireLogin();

$answer_id = $_GET['id'] ?? null;
$question_id = $_GET['question_id'] ?? null;

if (!$answer_id || !$question_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT a.*, u.username, u.full_name 
    FROM answers a 
    JOIN users u ON a.user_id = u.user_id 
    WHERE a.answer_id = ?
");
$stmt->execute([$answer_id]);
$answer = $stmt->fetch();

if (!$answer) {
    header('Location: view-question.php?id=' . $question_id . '&error=answer_not_found');
    exit;
}

if (!$auth->ownsAnswer($answer_id) && !$auth->isStaff()) {
    header('Location: view-question.php?id=' . $question_id . '&error=not_authorized');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    
    $errors = [];
    
    if (empty($content)) {
        $errors[] = 'Content is required';
    }
    
    if (empty($errors)) {
        try {
            // Update answer
            $stmt = $pdo->prepare("
                UPDATE answers 
                SET content = ? 
                WHERE answer_id = ?
            ");
            $stmt->execute([$content, $answer_id]);
            
            // Redirect with success message
            header('Location: view-question.php?id=' . $question_id . '&success=answer_updated');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to update answer: ' . $e->getMessage();
        }
    }
}

include 'templates/edit-answer.html.php';
?>