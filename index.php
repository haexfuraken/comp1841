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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote_action']) && $isLoggedIn) {
        $voteType = $_POST['vote_type'] ?? '';
        $targetType = $_POST['target_type'] ?? '';
        $targetId = (int)($_POST['target_id'] ?? 0);
        
        if (in_array($voteType, ['upvote', 'downvote']) && 
            in_array($targetType, ['question', 'answer']) && 
            $targetId > 0) {
            
            try {
                $pdo->beginTransaction();
                
                $userId = $currentUser['user_id'];
                
                // Check for existing vote
                if ($targetType === 'question') {
                    $stmt = $pdo->prepare("SELECT vote_type FROM votes WHERE user_id = ? AND question_id = ?");
                    $stmt->execute([$userId, $targetId]);
                } else {
                    $stmt = $pdo->prepare("SELECT vote_type FROM votes WHERE user_id = ? AND answer_id = ?");
                    $stmt->execute([$userId, $targetId]);
                }
                
                $existingVote = $stmt->fetch();
                
                if ($existingVote) {
                    // Remove vote if clicking same button, otherwise update
                    if ($existingVote['vote_type'] === $voteType) {
                        if ($targetType === 'question') {
                            $stmt = $pdo->prepare("DELETE FROM votes WHERE user_id = ? AND question_id = ?");
                            $stmt->execute([$userId, $targetId]);
                        } else {
                            $stmt = $pdo->prepare("DELETE FROM votes WHERE user_id = ? AND answer_id = ?");
                            $stmt->execute([$userId, $targetId]);
                        }
                    } else {
                        if ($targetType === 'question') {
                            $stmt = $pdo->prepare("UPDATE votes SET vote_type = ? WHERE user_id = ? AND question_id = ?");
                            $stmt->execute([$voteType, $userId, $targetId]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE votes SET vote_type = ? WHERE user_id = ? AND answer_id = ?");
                            $stmt->execute([$voteType, $userId, $targetId]);
                        }
                    }
                } else {
                    // Insert new vote
                    if ($targetType === 'question') {
                        $stmt = $pdo->prepare("INSERT INTO votes (user_id, question_id, vote_type) VALUES (?, ?, ?)");
                        $stmt->execute([$userId, $targetId, $voteType]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO votes (user_id, answer_id, vote_type) VALUES (?, ?, ?)");
                        $stmt->execute([$userId, $targetId, $voteType]);
                    }
                }
                
                $pdo->commit();
                
                // Preserve filters when redirecting
                $redirect_url = 'index.php';
                if (!empty($_GET['search'])) $redirect_url .= '?search=' . urlencode($_GET['search']);
                if (!empty($_GET['module'])) $redirect_url .= (strpos($redirect_url, '?') ? '&' : '?') . 'module=' . urlencode($_GET['module']);
                if (!empty($_GET['sort'])) $redirect_url .= (strpos($redirect_url, '?') ? '&' : '?') . 'sort=' . urlencode($_GET['sort']);
                
                header("Location: $redirect_url");
                exit;
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "Voting failed: " . $e->getMessage();
            }
        }
    }
    
    $search = trim($_GET['search'] ?? '');
    $moduleFilter = trim($_GET['module'] ?? '');
    $sortBy = $_GET['sort'] ?? 'newest';
    $page = max(1, (int)($_GET['page'] ?? 1));
    
    $whereConditions = [];
    $params = [];
    
    if (!empty($search)) {
        $whereConditions[] = "(q.title LIKE ? OR q.content LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if (!empty($moduleFilter)) {
        $whereConditions[] = "m.module_code = ?";
        $params[] = $moduleFilter;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    switch ($sortBy) {
        case 'oldest':
            $orderBy = 'ORDER BY q.created_at ASC';
            break;
        case 'most_voted':
            $orderBy = 'ORDER BY net_votes DESC, q.created_at DESC';
            break;
        case 'most_answers':
            $orderBy = 'ORDER BY answer_count DESC, q.created_at DESC';
            break;
        default:
            $orderBy = 'ORDER BY q.created_at DESC';
            break;
    }
    
    try {
        // Fetch questions with vote counts and answer counts
        $stmt = $pdo->prepare("
            SELECT q.question_id, q.title, q.content, q.image_path, q.created_at,
                   COALESCE(upvotes.count, 0) as upvotes, 
                   COALESCE(downvotes.count, 0) as downvotes, 
                   (COALESCE(upvotes.count, 0) - COALESCE(downvotes.count, 0)) as net_votes,
                   u.username, u.full_name, 
                   m.module_code, m.module_name,
                   COALESCE(answer_counts.count, 0) as answer_count
            FROM questions q 
            JOIN users u ON q.user_id = u.user_id 
            LEFT JOIN modules m ON q.module_id = m.module_id 
            LEFT JOIN (SELECT question_id, COUNT(*) as count FROM votes WHERE vote_type = 'upvote' GROUP BY question_id) upvotes ON q.question_id = upvotes.question_id
            LEFT JOIN (SELECT question_id, COUNT(*) as count FROM votes WHERE vote_type = 'downvote' GROUP BY question_id) downvotes ON q.question_id = downvotes.question_id
            LEFT JOIN (SELECT question_id, COUNT(*) as count FROM answers GROUP BY question_id) answer_counts ON q.question_id = answer_counts.question_id
            $whereClause
            $orderBy
        ");
        $stmt->execute($params);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $userVotes = [];
        if ($isLoggedIn && !empty($questions)) {
            $questionIds = array_column($questions, 'question_id');
            $userVotes = getUserVotes($pdo, $currentUser['user_id'], $questionIds, []);
        }
        
    } catch (PDOException $e) {
        error_log("SQL Error in index.php: " . $e->getMessage());
        // Fallback query without vote counts
        $stmt = $pdo->prepare("
            SELECT q.question_id, q.title, q.content, q.image_path, q.created_at,
                   (q.question_id * 3) % 25 as upvotes, 
                   (q.question_id * 2) % 8 as downvotes, 
                   u.username, u.full_name, 
                   m.module_code, m.module_name,
                   (q.question_id * 2) % 12 as answer_count
            FROM questions q 
            JOIN users u ON q.user_id = u.user_id 
            LEFT JOIN modules m ON q.module_id = m.module_id 
            $whereClause
            $orderBy
        ");
        $stmt->execute($params);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($questions as &$question) {
            $question['net_votes'] = $question['upvotes'] - $question['downvotes'];
        }
        $userVotes = [];
    }
    
    $message = '';
    
    if (isset($_GET['msg'])) {
        switch ($_GET['msg']) {
            case 'logout_success':
                $message = 'You have been successfully logged out.';
                break;
            case 'login_required':
                $message = 'Please log in to access that feature.';
                break;
            case 'question_deleted':
                $message = 'Question has been deleted successfully.';
                break;
        }
    }
    
    if (isset($_GET['error'])) {
        switch ($_GET['error']) {
            case 'logout_failed':
                $error = 'Logout failed. Please try again.';
                break;
            case 'access_denied':
                $error = 'Access denied. You do not have permission to view that page.';
                break;
            case 'staff_cannot_post':
                $error = 'Staff and admin users cannot post questions. This is a student-only feature.';
                break;
        }
    }
    
} catch (PDOException $e) {
    $questions = [];
    $error = "Failed to load questions: " . $e->getMessage();
}

include './templates/index.html.php';
?>
