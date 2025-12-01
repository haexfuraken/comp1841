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

    $answers = [];
    try {
        $stmt = $pdo->prepare("
            SELECT a.answer_id, a.content, a.created_at, a.is_solution,
                   q.question_id, q.title as question_title,
                   u.username as question_author,
                   m.module_code, m.module_name
            FROM answers a 
            JOIN questions q ON a.question_id = q.question_id 
            JOIN users u ON q.user_id = u.user_id
            LEFT JOIN modules m ON q.module_id = m.module_id
            WHERE a.user_id = ? 
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$currentUser['user_id']]);
        $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching answers: " . $e->getMessage();
        $answers = [];
    }

} catch (PDOException $e) {
    $error = "Database connection failed: " . $e->getMessage();
    $answers = [];
}

include 'templates/my-answers.html.php';