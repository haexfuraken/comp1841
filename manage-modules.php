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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_module':
                $moduleName = trim($_POST['module_name'] ?? '');
                $moduleCode = trim($_POST['module_code'] ?? '');
                $description = trim($_POST['description'] ?? '');
                
                // Validation
                if (empty($moduleName) || empty($moduleCode)) {
                    $error = 'Module name and code are required.';
                } else {
                    try {
                        // Check if module code already exists
                        $stmt = $pdo->prepare("SELECT module_id FROM modules WHERE module_code = ?");
                        $stmt->execute([$moduleCode]);
                        if ($stmt->fetch()) {
                            $error = 'Module code already exists.';
                        } else {
                            // Insert new module
                            $stmt = $pdo->prepare("
                                INSERT INTO modules (module_name, module_code, description) 
                                VALUES (?, ?, ?)
                            ");
                            $stmt->execute([$moduleName, $moduleCode, $description]);
                            
                            $message = "Module '$moduleCode - $moduleName' has been created successfully.";
                        }
                    } catch (PDOException $e) {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'edit_module':
                $moduleId = (int)($_POST['module_id'] ?? 0);
                $moduleName = trim($_POST['module_name'] ?? '');
                $moduleCode = trim($_POST['module_code'] ?? '');
                
                if (empty($moduleName) || empty($moduleCode)) {
                    $error = 'Module name and code are required.';
                } else {
                    try {
                        // Check if module code already exists for other modules
                        $stmt = $pdo->prepare("SELECT module_id FROM modules WHERE module_code = ? AND module_id != ?");
                        $stmt->execute([$moduleCode, $moduleId]);
                        if ($stmt->fetch()) {
                            $error = 'Module code already exists.';
                        } else {
                            // Update module
                            $stmt = $pdo->prepare("
                                UPDATE modules 
                                SET module_name = ?, module_code = ? 
                                WHERE module_id = ?
                            ");
                            $stmt->execute([$moduleName, $moduleCode, $moduleId]);
                            
                            $message = "Module '$moduleCode - $moduleName' has been updated successfully.";
                        }
                    } catch (PDOException $e) {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'delete_module':
                $moduleId = (int)($_POST['module_id'] ?? 0);
                
                try {
                    // Get module details before deletion
                    $stmt = $pdo->prepare("SELECT module_code, module_name FROM modules WHERE module_id = ?");
                    $stmt->execute([$moduleId]);
                    $moduleToDelete = $stmt->fetch();
                    
                    if ($moduleToDelete) {
                        // Set module_id to NULL for all questions associated with this module
                        $stmt = $pdo->prepare("UPDATE questions SET module_id = NULL WHERE module_id = ?");
                        $stmt->execute([$moduleId]);
                        
                        // Delete module
                        $stmt = $pdo->prepare("DELETE FROM modules WHERE module_id = ?");
                        $stmt->execute([$moduleId]);
                        
                        $message = "Module '{$moduleToDelete['module_code']} - {$moduleToDelete['module_name']}' has been deleted successfully.";
                    } else {
                        $error = 'Module not found.';
                    }
                } catch (PDOException $e) {
                    $error = 'Database error: ' . $e->getMessage();
                }
                break;
        }
    }
}

$search = trim($_GET['search'] ?? '');

$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(module_name LIKE ? OR module_code LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereClause = '';
if (!empty($whereConditions)) {
    $whereClause = "WHERE " . implode(" AND ", $whereConditions);
}

try {
    // Get modules with question counts
    $stmt = $pdo->prepare("
        SELECT m.*, 
               COUNT(q.question_id) as question_count,
               COUNT(DISTINCT q.user_id) as author_count
        FROM modules m
        LEFT JOIN questions q ON m.module_id = q.module_id
        $whereClause
        GROUP BY m.module_id, m.module_code, m.module_name, m.created_at
        ORDER BY m.module_code
    ");
    $stmt->execute($params);
    $modules = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
    $modules = [];
}

// Get module for editing if edit ID is provided
$editModule = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM modules WHERE module_id = ?");
        $stmt->execute([$editId]);
        $editModule = $stmt->fetch();
    } catch (PDOException $e) {
        $error = 'Error loading module data.';
    }
}

include 'templates/manage-modules.html.php';
?>