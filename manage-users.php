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
            case 'add_user':
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $fullName = trim($_POST['full_name'] ?? '');
                $password = trim($_POST['password'] ?? '');
                $role = trim($_POST['role'] ?? '');
                
                // Role validation based on current user's role
                $allowedRoles = [];
                if ($auth->isAdmin()) {
                    $allowedRoles = ['student', 'staff']; // Admin cannot create other admins
                } elseif ($auth->isStaff()) {
                    $allowedRoles = ['student']; // Staff can only create students
                }
                
                if (empty($username) || empty($email) || empty($fullName) || empty($password) || empty($role)) {
                    $error = 'All fields are required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please enter a valid email address.';
                } elseif (strlen($password) < 6) {
                    $error = 'Password must be at least 6 characters long.';
                } elseif (!in_array($role, $allowedRoles)) {
                    if ($role === 'admin') {
                        $error = 'You cannot create or promote users to admin role.';
                    } else {
                        $error = 'You do not have permission to create users with this role.';
                    }
                } else {
                    try {
                        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
                        $stmt->execute([$username, $email]);
                        if ($stmt->fetch()) {
                            $error = 'Username or email already exists.';
                        } else {
                            // Insert new user
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("
                                INSERT INTO users (username, email, password, full_name, role) 
                                VALUES (?, ?, ?, ?, ?)
                            ");
                            $stmt->execute([$username, $email, $hashedPassword, $fullName, $role]);
                            
                            $message = "User '$username' has been created successfully.";
                        }
                    } catch (PDOException $e) {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'edit_user':
                $userId = (int)($_POST['user_id'] ?? 0);
                
                if (!$auth->canEditUser($userId, $currentUser['user_id'])) {
                    $error = 'You do not have permission to edit this user.';
                    break;
                }
                
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $fullName = trim($_POST['full_name'] ?? '');
                $role = trim($_POST['role'] ?? '');
                
                // Role validation based on current user's role
                $allowedRoles = [];
                if ($auth->isAdmin()) {
                    $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    $targetUser = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $allowedRoles = ['student', 'staff']; // Admin cannot promote others to admin
                    if ($targetUser && $targetUser['role'] === 'admin') {
                        $allowedRoles[] = 'admin'; // Allow keeping existing admin role
                    }
                } elseif ($auth->isStaff()) {
                    $allowedRoles = ['student']; // Staff can only manage students
                }
                
                if (empty($username) || empty($email) || empty($fullName) || empty($role)) {
                    $error = 'All fields are required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please enter a valid email address.';
                } elseif (!in_array($role, $allowedRoles)) {
                    if ($role === 'admin') {
                        $error = 'You cannot promote users to admin role.';
                    } else {
                        $error = 'You do not have permission to assign this role.';
                    }
                } else {
                    try {
                        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
                        $stmt->execute([$username, $email, $userId]);
                        if ($stmt->fetch()) {
                            $error = 'Username or email already exists.';
                        } else {
                            // Update user
                            $stmt = $pdo->prepare("
                                UPDATE users 
                                SET username = ?, email = ?, full_name = ?, role = ? 
                                WHERE user_id = ?
                            ");
                            $stmt->execute([$username, $email, $fullName, $role, $userId]);
                            
                            $message = "User '$username' has been updated successfully.";
                        }
                    } catch (PDOException $e) {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'delete_user':
                $userId = (int)($_POST['user_id'] ?? 0);
                
                if ($userId === $currentUser['user_id']) {
                    $error = 'You cannot delete your own account.';
                } elseif (!$auth->canEditUser($userId, $currentUser['user_id'])) {
                    $error = 'You do not have permission to delete this user.';
                } else {
                    try {
                        // Get user details before deletion
                        $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
                        $stmt->execute([$userId]);
                        $userToDelete = $stmt->fetch();
                        
                        if ($userToDelete) {
                            // Delete user
                            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                            $stmt->execute([$userId]);
                            
                            $message = "User '{$userToDelete['username']}' has been deleted successfully.";
                        } else {
                            $error = 'User not found.';
                        }
                    } catch (PDOException $e) {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                }
                break;
        }
    }
}

$search = trim($_GET['search'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');

$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($roleFilter) && in_array($roleFilter, ['student', 'staff', 'admin'])) {
    $whereConditions[] = "role = ?";
    $params[] = $roleFilter;
}

$whereClause = '';
if (!empty($whereConditions)) {
    $whereClause = "WHERE " . implode(" AND ", $whereConditions);
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

try {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM users $whereClause");
    $countStmt->execute($params);
    $totalUsers = $countStmt->fetchColumn();
    $totalPages = ceil($totalUsers / $perPage);
    
    // Get users for current page
    $stmt = $pdo->prepare("
        SELECT user_id, username, email, full_name, role, created_at
        FROM users 
        $whereClause
        ORDER BY created_at DESC
        LIMIT $perPage OFFSET $offset
    ");
    $stmt->execute($params);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
    $users = [];
    $totalUsers = 0;
    $totalPages = 0;
}

include 'templates/manage-users.html.php';
?>