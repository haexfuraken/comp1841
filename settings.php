<?php
include 'includes/config.php';
include 'includes/auth.php';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $auth = new Auth($pdo);
    $currentUser = $auth->getCurrentUser();
    
    if (!$auth->isLoggedIn()) {
        header('Location: login.php?msg=login_required');
        exit();
    }
    
    $message = '';
    $error = '';
    
    if (isset($_GET['updated'])) {
        switch ($_GET['updated']) {
            case 'profile':
                $message = 'Profile updated successfully!';
                break;
            case 'password':
                $message = 'Password changed successfully!';
                break;
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'update_profile') {
                $username = trim($_POST['username']);
                $full_name = trim($_POST['full_name']);
                $email = trim($_POST['email']);
                
                if (empty($username) || empty($full_name) || empty($email)) {
                    $error = 'All fields are required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please enter a valid email address.';
                } elseif (strlen($username) < 3) {
                    $error = 'Username must be at least 3 characters long.';
                } else {
                    try {
                        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
                        $stmt->execute([$username, $currentUser['user_id']]);
                        
                        if ($stmt->fetch()) {
                            $error = 'Username is already taken. Please choose another one.';
                        } else {
                            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
                            $stmt->execute([$email, $currentUser['user_id']]);
                            
                            if ($stmt->fetch()) {
                                $error = 'Email is already in use by another account.';
                            } else {
                                $stmt = $pdo->prepare("
                                    UPDATE users 
                                    SET username = ?, full_name = ?, email = ? 
                                    WHERE user_id = ?
                                ");
                                $stmt->execute([$username, $full_name, $email, $currentUser['user_id']]);
                                
                                $_SESSION['user']['username'] = $username;
                                $_SESSION['user']['full_name'] = $full_name;
                                $_SESSION['user']['email'] = $email;
                                
                                $currentUser = $auth->getCurrentUser();
                                
                                header('Location: settings.php?updated=profile');
                                exit;
                            }
                        }
                    } catch (PDOException $e) {
                        $error = 'Failed to update profile: ' . $e->getMessage();
                    }
                }
            } elseif ($_POST['action'] === 'change_password') {
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                
                if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                    $error = 'All password fields are required.';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'New passwords do not match.';
                } elseif (strlen($new_password) < 6) {
                    $error = 'New password must be at least 6 characters long.';
                } else {
                    try {
                        $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
                        $stmt->execute([$currentUser['user_id']]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (!password_verify($current_password, $user['password'])) {
                            $error = 'Current password is incorrect.';
                        } else {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("
                                UPDATE users 
                                SET password = ? 
                                WHERE user_id = ?
                            ");
                            $stmt->execute([$hashed_password, $currentUser['user_id']]);
                            
                            header('Location: settings.php?updated=password');
                            exit;
                        }
                    } catch (PDOException $e) {
                        $error = 'Failed to change password: ' . $e->getMessage();
                    }
                }
            }
        }
    }
    
} catch (PDOException $e) {
    $error = "Database connection failed: " . $e->getMessage();
}

include './templates/settings.html.php';
?>