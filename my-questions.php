<?php
include 'includes/config.php';
include 'includes/auth.php';
include 'includes/functions.php';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $auth = new Auth($pdo);
    $currentUser = $auth->getCurrentUser();
    $isLoggedIn = $auth->isLoggedIn();
    
    if (!$isLoggedIn) {
        header('Location: login.php?msg=login_required');
        exit();
    }
    
    $stmt = $pdo->prepare("
        SELECT q.question_id, q.title, q.content, q.image_path, q.created_at,
               m.module_code, m.module_name,
               COUNT(a.answer_id) as answer_count
        FROM questions q 
        LEFT JOIN modules m ON q.module_id = m.module_id 
        LEFT JOIN answers a ON q.question_id = a.question_id
        WHERE q.user_id = ?
        GROUP BY q.question_id, q.title, q.content, q.image_path, q.created_at,
                 m.module_code, m.module_name
        ORDER BY q.created_at DESC
    ");
    $stmt->execute([$currentUser['user_id']]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $questions = [];
    $error = "Failed to load questions: " . $e->getMessage();
}

include './templates/my-questions.html.php';
?>