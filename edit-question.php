<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$pdo = new PDO($dsn, $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$auth = new Auth($pdo);
$auth->requireLogin();

$current_user = $auth->getCurrentUser();
$question_id = $_GET['id'] ?? null;

if (!$question_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT q.*, u.username, u.full_name, m.module_code, m.module_name 
    FROM questions q 
    JOIN users u ON q.user_id = u.user_id 
    LEFT JOIN modules m ON q.module_id = m.module_id 
    WHERE q.question_id = ?
");
$stmt->execute([$question_id]);
$question = $stmt->fetch();

if (!$question) {
    header('Location: index.php?error=question_not_found');
    exit;
}

if (!$auth->ownsQuestion($question_id) && !$auth->isStaff()) {
    header('Location: view-question.php?id=' . $question_id . '&error=not_authorized');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $module_id = !empty($_POST['module_id']) ? (int)$_POST['module_id'] : null;
    
    $errors = [];
    if (empty($title)) {
        $errors[] = 'Title is required';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Title must be less than 255 characters';
    }
    
    if (empty($content)) {
        $errors[] = 'Content is required';
    }
    
    // Validate module_id if provided
    if ($module_id !== null) {
        $moduleCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE module_id = ?");
        $moduleCheckStmt->execute([$module_id]);
        if ($moduleCheckStmt->fetchColumn() == 0) {
            $errors[] = 'Selected module does not exist';
        }
    }
    
    if (empty($errors)) {
        try {
            // Update question
            $stmt = $pdo->prepare("
                UPDATE questions 
                SET title = ?, content = ?, module_id = ? 
                WHERE question_id = ?
            ");
            $stmt->execute([$title, $content, $module_id, $question_id]);
            
            header('Location: view-question.php?id=' . $question_id . '&success=question_updated');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Failed to update question: ' . $e->getMessage();
        }
    }
}

// Get available modules from database
$moduleStmt = $pdo->prepare("SELECT module_id, module_code, module_name FROM modules ORDER BY module_code");
$moduleStmt->execute();
$modules = $moduleStmt->fetchAll();

include 'templates/edit-question.html.php';
?>