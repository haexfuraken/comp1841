<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireAuth('staff');

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$auth = new Auth($pdo);
$currentUser = $auth->getCurrentUser();
$isLoggedIn = $auth->isLoggedIn();

$error = '';
$message = '';

$userId = (int)($_GET['user_id'] ?? 0);
if (!$userId) {
    header('Location: manage-users.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $targetUser = $stmt->fetch();
    
    if (!$targetUser) {
        header('Location: manage-users.php');
        exit;
    }
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete_question':
                $questionId = (int)($_POST['question_id'] ?? 0);
                try {
                    $stmt = $pdo->prepare("SELECT title FROM questions WHERE question_id = ? AND user_id = ?");
                    $stmt->execute([$questionId, $userId]);
                    $question = $stmt->fetch();
                    
                    if ($question) {
                        $stmt = $pdo->prepare("DELETE FROM questions WHERE question_id = ? AND user_id = ?");
                        $stmt->execute([$questionId, $userId]);
                        
                        $message = "Question has been deleted successfully.";
                    } else {
                        $error = 'Question not found.';
                    }
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
                break;
                
            case 'delete_answer':
                $answerId = (int)($_POST['answer_id'] ?? 0);
                try {
                    $stmt = $pdo->prepare("SELECT content FROM answers WHERE answer_id = ? AND user_id = ?");
                    $stmt->execute([$answerId, $userId]);
                    $answer = $stmt->fetch();
                    
                    if ($answer) {
                        $stmt = $pdo->prepare("DELETE FROM answers WHERE answer_id = ? AND user_id = ?");
                        $stmt->execute([$answerId, $userId]);
                        
                        $message = "Answer has been deleted successfully.";
                    } else {
                        $error = 'Answer not found.';
                    }
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
                break;
        }
    }
}

$questions = [];
try {
    $stmt = $pdo->prepare("
        SELECT q.*, m.module_code, m.module_name,
               (SELECT COUNT(*) FROM answers WHERE question_id = q.question_id) as answer_count,
               COALESCE((SELECT SUM(CASE WHEN vote_type = 'upvote' THEN 1 WHEN vote_type = 'downvote' THEN -1 ELSE 0 END)
                        FROM votes 
                        WHERE question_id = q.question_id), 0) as net_votes
        FROM questions q
        LEFT JOIN modules m ON q.module_id = m.module_id
        WHERE q.user_id = ?
        ORDER BY q.created_at DESC
    ");
    $stmt->execute([$userId]);
    $questions = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

$answers = [];
try {
    $stmt = $pdo->prepare("
        SELECT a.*, q.title as question_title, q.question_id,
               COALESCE((SELECT SUM(CASE WHEN vote_type = 'upvote' THEN 1 WHEN vote_type = 'downvote' THEN -1 ELSE 0 END)
                        FROM votes 
                        WHERE answer_id = a.answer_id), 0) as net_votes
        FROM answers a
        JOIN questions q ON a.question_id = q.question_id
        WHERE a.user_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$userId]);
    $answers = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

include 'templates/user-activity.html.php';
?>