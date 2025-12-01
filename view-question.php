<?php
include 'includes/config.php';
include 'includes/auth.php';
include 'includes/functions.php';

$message = '';
$error = '';
$question = null;
$answers = [];

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'answer_posted':
            $message = "Answer added successfully!";
            break;
        case 'question_updated':
            $message = "Question updated successfully!";
            break;
        case 'answer_updated':
            $message = "Answer updated successfully!";
            break;
        case 'solution_updated':
            $message = "Answer solution status updated successfully!";
            break;
    }
}

if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'module_assigned':
            $message = 'Module assignment has been updated successfully.';
            break;
        case 'question_deleted':
            $message = 'Question has been deleted successfully.';
            break;
    }
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $auth = new Auth($pdo);
    $currentUser = $auth->getCurrentUser();
    $isLoggedIn = $auth->isLoggedIn();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_question']) && $isLoggedIn) {
        if ($currentUser['role'] === 'staff' || $currentUser['role'] === 'admin') {
            $questionId = (int)($_POST['question_id'] ?? 0);
            
            if ($questionId > 0) {
                try {
                    $pdo->beginTransaction();
                    
                    $stmt = $pdo->prepare("SELECT title, user_id FROM questions WHERE question_id = ?");
                    $stmt->execute([$questionId]);
                    $questionDetails = $stmt->fetch();
                    
                    if ($questionDetails) {
                        // Delete related data
                        $stmt = $pdo->prepare("DELETE FROM votes WHERE question_id = ?");
                        $stmt->execute([$questionId]);
                        
                        $stmt = $pdo->prepare("DELETE FROM votes WHERE answer_id IN (SELECT answer_id FROM answers WHERE question_id = ?)");
                        $stmt->execute([$questionId]);
                        
                        $stmt = $pdo->prepare("DELETE FROM answers WHERE question_id = ?");
                        $stmt->execute([$questionId]);
                        
                        $stmt = $pdo->prepare("DELETE FROM questions WHERE question_id = ?");
                        $stmt->execute([$questionId]);
                        
                        $pdo->commit();
                        
                        header("Location: index.php?msg=question_deleted");
                        exit;
                    }
                    
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $error = "Delete failed: " . $e->getMessage();
                }
            }
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_module']) && $isLoggedIn) {
        if ($currentUser['role'] === 'staff' || $currentUser['role'] === 'admin') {
            $questionId = (int)($_POST['question_id'] ?? 0);
            $newModuleId = $_POST['new_module_id'] ?? '';
            
            if ($questionId > 0) {
                try {
                    $pdo->beginTransaction();
                    
                    $stmt = $pdo->prepare("SELECT title, module_id FROM questions WHERE question_id = ?");
                    $stmt->execute([$questionId]);
                    $questionDetails = $stmt->fetch();
                    
                    if ($questionDetails) {
                        $oldModuleId = $questionDetails['module_id'];
                        
                        // Update question module
                        if (empty($newModuleId)) {
                            $stmt = $pdo->prepare("UPDATE questions SET module_id = NULL WHERE question_id = ?");
                            $stmt->execute([$questionId]);
                            $actionDescription = "Removed module assignment from question: {$questionDetails['title']}";
                        } else {
                            // Verify module exists
                            $moduleCheckStmt = $pdo->prepare("SELECT module_code, module_name FROM modules WHERE module_id = ?");
                            $moduleCheckStmt->execute([$newModuleId]);
                            $newModule = $moduleCheckStmt->fetch();
                            
                            if ($newModule) {
                                $stmt = $pdo->prepare("UPDATE questions SET module_id = ? WHERE question_id = ?");
                                $stmt->execute([$newModuleId, $questionId]);
                                $actionDescription = "Assigned question '{$questionDetails['title']}' to module: {$newModule['module_code']} - {$newModule['module_name']}";
                            } else {
                                throw new Exception("Selected module does not exist");
                            }
                        }
                        
                        $pdo->commit();
                        
                        header("Location: view-question.php?id=$questionId&msg=module_assigned");
                        exit;
                    }
                    
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = "Module assignment failed: " . $e->getMessage();
                }
            }
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote_action']) && $isLoggedIn) {
        $voteType = $_POST['vote_type'] ?? '';
        $targetType = $_POST['target_type'] ?? '';
        $targetId = (int)($_POST['target_id'] ?? 0);
        $questionId = $_GET['id'] ?? null;
        
        if (in_array($voteType, ['upvote', 'downvote']) && 
            in_array($targetType, ['question', 'answer']) && 
            $targetId > 0) {
            
            try {
                $pdo->beginTransaction();
                
                $userId = $currentUser['user_id'];
                
                // Check if user already voted
                if ($targetType === 'question') {
                    $stmt = $pdo->prepare("SELECT vote_type FROM votes WHERE user_id = ? AND question_id = ?");
                    $stmt->execute([$userId, $targetId]);
                } else {
                    $stmt = $pdo->prepare("SELECT vote_type FROM votes WHERE user_id = ? AND answer_id = ?");
                    $stmt->execute([$userId, $targetId]);
                }
                
                $existingVote = $stmt->fetch();
                
                if ($existingVote) {
                    if ($existingVote['vote_type'] === $voteType) {
                        // Same vote type - remove vote
                        if ($targetType === 'question') {
                            $stmt = $pdo->prepare("DELETE FROM votes WHERE user_id = ? AND question_id = ?");
                            $stmt->execute([$userId, $targetId]);
                        } else {
                            $stmt = $pdo->prepare("DELETE FROM votes WHERE user_id = ? AND answer_id = ?");
                            $stmt->execute([$userId, $targetId]);
                        }
                    } else {
                        // Different vote type - update vote
                        if ($targetType === 'question') {
                            $stmt = $pdo->prepare("UPDATE votes SET vote_type = ? WHERE user_id = ? AND question_id = ?");
                            $stmt->execute([$voteType, $userId, $targetId]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE votes SET vote_type = ? WHERE user_id = ? AND answer_id = ?");
                            $stmt->execute([$voteType, $userId, $targetId]);
                        }
                    }
                } else {
                    // New vote
                    if ($targetType === 'question') {
                        $stmt = $pdo->prepare("INSERT INTO votes (user_id, question_id, vote_type) VALUES (?, ?, ?)");
                        $stmt->execute([$userId, $targetId, $voteType]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO votes (user_id, answer_id, vote_type) VALUES (?, ?, ?)");
                        $stmt->execute([$userId, $targetId, $voteType]);
                    }
                }
                
                $pdo->commit();
                
                header("Location: view-question.php?id=" . $questionId);
                exit;
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Voting failed: " . $e->getMessage();
            }
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_solution']) && $isLoggedIn) {
        $answerId = (int)($_POST['answer_id'] ?? 0);
        $questionId = $_GET['id'] ?? null;
        
        if ($answerId > 0 && $questionId) {
            try {
                $stmt = $pdo->prepare("SELECT user_id FROM questions WHERE question_id = ?");
                $stmt->execute([$questionId]);
                $question = $stmt->fetch();
                
                if ($question && $question['user_id'] == $currentUser['user_id']) {
                    $pdo->beginTransaction();
                    
                    // Check current solution status
                    $stmt = $pdo->prepare("SELECT is_solution FROM answers WHERE answer_id = ? AND question_id = ?");
                    $stmt->execute([$answerId, $questionId]);
                    $answer = $stmt->fetch();
                    
                    if ($answer) {
                        if ($answer['is_solution']) {
                            // Unaccept the answer
                            $stmt = $pdo->prepare("UPDATE answers SET is_solution = 0 WHERE answer_id = ?");
                            $stmt->execute([$answerId]);
                            
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM answers WHERE question_id = ? AND is_solution = 1");
                            $stmt->execute([$questionId]);
                            $solutionCount = $stmt->fetchColumn();
                            
                            if ($solutionCount == 0) {
                                $stmt = $pdo->prepare("UPDATE questions SET is_answered = 0 WHERE question_id = ?");
                                $stmt->execute([$questionId]);
                            }
                        } else {
                            // Accept the answer (remove any previous solutions first)
                            $stmt = $pdo->prepare("UPDATE answers SET is_solution = 0 WHERE question_id = ?");
                            $stmt->execute([$questionId]);
                            
                            $stmt = $pdo->prepare("UPDATE answers SET is_solution = 1 WHERE answer_id = ?");
                            $stmt->execute([$answerId]);
                            
                            // Update question as answered
                            $stmt = $pdo->prepare("UPDATE questions SET is_answered = 1 WHERE question_id = ?");
                            $stmt->execute([$questionId]);
                        }
                        
                        $pdo->commit();
                        header("Location: view-question.php?id=" . $questionId . "&success=solution_updated");
                        exit;
                    }
                } else {
                    $error = "You can only accept answers for your own questions.";
                }
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Failed to update solution status: " . $e->getMessage();
            }
        }
    }

    $question_id = $_GET['id'] ?? null;
    
    if (!$question_id || !is_numeric($question_id)) {
        $error = "Invalid question ID.";
    } else {
        // Fetch question with user and module information including vote counts
        $stmt = $pdo->prepare("
            SELECT q.question_id, q.title, q.content, q.image_path, q.is_answered,
                   q.created_at, q.module_id,
                   COALESCE(upvotes.count, 0) as upvotes,
                   COALESCE(downvotes.count, 0) as downvotes,
                   (COALESCE(upvotes.count, 0) - COALESCE(downvotes.count, 0)) as net_votes,
                   u.user_id, u.username, u.full_name, u.role,
                   m.module_code, m.module_name
            FROM questions q 
            JOIN users u ON q.user_id = u.user_id 
            LEFT JOIN modules m ON q.module_id = m.module_id 
            LEFT JOIN (SELECT question_id, COUNT(*) as count FROM votes WHERE vote_type = 'upvote' GROUP BY question_id) upvotes ON q.question_id = upvotes.question_id
            LEFT JOIN (SELECT question_id, COUNT(*) as count FROM votes WHERE vote_type = 'downvote' GROUP BY question_id) downvotes ON q.question_id = downvotes.question_id
            WHERE q.question_id = ?
        ");
        $stmt->execute([$question_id]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$question) {
            $error = "Question not found or not available.";
        } else {
            // Fetch answers for this question with vote counts
            $stmt = $pdo->prepare("
                SELECT a.answer_id, a.content, a.is_solution, a.created_at,
                       COALESCE(upvotes.count, 0) as upvotes,
                       COALESCE(downvotes.count, 0) as downvotes,
                       (COALESCE(upvotes.count, 0) - COALESCE(downvotes.count, 0)) as net_votes,
                       u.username, u.full_name, u.role
                FROM answers a
                JOIN users u ON a.user_id = u.user_id
                LEFT JOIN (SELECT answer_id, COUNT(*) as count FROM votes WHERE vote_type = 'upvote' GROUP BY answer_id) upvotes ON a.answer_id = upvotes.answer_id
                LEFT JOIN (SELECT answer_id, COUNT(*) as count FROM votes WHERE vote_type = 'downvote' GROUP BY answer_id) downvotes ON a.answer_id = downvotes.answer_id
                WHERE a.question_id = ?
                ORDER BY a.is_solution DESC, net_votes DESC, a.created_at ASC
            ");
            $stmt->execute([$question_id]);
            $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $userVotes = [];
            if ($isLoggedIn) {
                $answerIds = array_column($answers, 'answer_id');
                $userVotes = getUserVotes($pdo, $currentUser['user_id'], [$question_id], $answerIds);
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn) {
                $comment_content = trim($_POST['comment'] ?? '');
                
                if (empty($comment_content)) {
                    $error = "Answer cannot be empty.";
                } else {
                    // Insert new answer
                    $stmt = $pdo->prepare("
                        INSERT INTO answers (question_id, user_id, content) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$question_id, $currentUser['user_id'], $comment_content]);
                    
                    header('Location: view-question.php?id=' . $question_id . '&success=answer_posted');
                    exit;
                }
            }
        }
    }
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$allModules = [];
if ($isLoggedIn && ($currentUser['role'] === 'staff' || $currentUser['role'] === 'admin')) {
    try {
        $moduleStmt = $pdo->prepare("SELECT module_id, module_code, module_name FROM modules ORDER BY module_code");
        $moduleStmt->execute();
        $allModules = $moduleStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $allModules = [];
    }
}

include './templates/view-question.html.php';
?>