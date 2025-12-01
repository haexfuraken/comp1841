<?php
include 'includes/config.php';
include 'includes/auth.php';

$message = '';
$error = '';

if (isset($_GET['posted']) && $_GET['posted'] == '1') {
    $message = "Question posted successfully!";
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $auth = new Auth($pdo);
    $auth->requireLogin();
    $currentUser = $auth->getCurrentUser();
    $isLoggedIn = $auth->isLoggedIn();
    
    // Prevent admin/staff from posting questions
    if ($auth->isStaff()) {
        header("Location: index.php?error=staff_cannot_post");
        exit;
    }
    
    // Fetch modules for dropdown
    $moduleStmt = $pdo->prepare("SELECT module_id, module_code, module_name FROM modules ORDER BY module_code");
    $moduleStmt->execute();
    $modules = $moduleStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $user_id = $currentUser['user_id'];
        $module_id = !empty($_POST['module_id']) ? (int)$_POST['module_id'] : null;
        
        if (empty($title) || empty($content)) {
            $error = "Title and content are required.";
        } elseif (strlen($title) > 200) {
            $error = "Title must be 200 characters or less.";
        } else {
            if ($module_id !== null) {
                $moduleCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE module_id = ?");
                $moduleCheckStmt->execute([$module_id]);
                if ($moduleCheckStmt->fetchColumn() == 0) {
                    $error = "Selected module does not exist.";
                }
            }
        }
        
        if (empty($error)) {
            // Handle image upload if provided
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileInfo = pathinfo($_FILES['image']['name']);
                $fileName = uniqid() . '.' . $fileInfo['extension'];
                $targetPath = $uploadDir . $fileName;
                
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array(strtolower($fileInfo['extension']), $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $imagePath = $targetPath;
                    } else {
                        $error = "Failed to upload image.";
                    }
                } else {
                    $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
                }
            }
            
            if (empty($error)) {
                $insertStmt = $pdo->prepare("
                    INSERT INTO questions (title, content, image_path, user_id, module_id) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $insertStmt->execute([$title, $content, $imagePath, $user_id, $module_id]);
                
                header('Location: add-question.php?posted=1');
                exit;
            }
        }
    }
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

include './templates/add-question.html.php';
?>