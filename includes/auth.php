<?php
class Auth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function register($username, $email, $password, $full_name, $role = 'student') {
        try {
            $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password, full_name, role) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$username, $email, $hashedPassword, $full_name, $role]);
            
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    public function login($username, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT user_id, username, email, password, full_name, role 
                FROM users 
                WHERE (username = ? OR email = ?)
            ");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;
                

                
                try {
                    $session_id = session_id();
                    $stmt = $this->pdo->prepare("
                        INSERT INTO user_sessions (session_id, user_id, ip_address, user_agent, expires_at) 
                        VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))
                    ");
                    $stmt->execute([
                        $session_id, 
                        $user['user_id'], 
                        $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                        $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                    ]);
                } catch (PDOException $e) {
                }
                
                return ['success' => true, 'message' => 'Login successful', 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            try {
                $session_id = session_id();
                $stmt = $this->pdo->prepare("DELETE FROM user_sessions WHERE session_id = ?");
                $stmt->execute([$session_id]);
            } catch (PDOException $e) {
            }
        }
        
        session_unset();
        session_destroy();
        return ['success' => true, 'message' => 'Logout successful'];
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'] ?? '',
                'full_name' => $_SESSION['full_name'],
                'role' => $_SESSION['role']
            ];
        }
        return null;
    }
    
    public function ownsQuestion($question_id, $user_id = null) {
        $user_id = $user_id ?? $_SESSION['user_id'] ?? null;
        if (!$user_id) return false;
        
        $stmt = $this->pdo->prepare("SELECT user_id FROM questions WHERE question_id = ?");
        $stmt->execute([$question_id]);
        $question = $stmt->fetch();
        
        return $question && $question['user_id'] == $user_id;
    }
    
    public function ownsAnswer($answer_id, $user_id = null) {
        $user_id = $user_id ?? $_SESSION['user_id'] ?? null;
        if (!$user_id) return false;
        
        $stmt = $this->pdo->prepare("SELECT user_id FROM answers WHERE answer_id = ?");
        $stmt->execute([$answer_id]);
        $answer = $stmt->fetch();
        
        return $answer && $answer['user_id'] == $user_id;
    }
    
    public function ownsComment($comment_id, $user_id = null) {
        return $this->ownsAnswer($comment_id, $user_id);
    }
    
    public function isStaff($user_id = null) {
        $user_id = $user_id ?? $_SESSION['user_id'] ?? null;
        if (!$user_id) return false;
        
        $role = $_SESSION['role'] ?? null;
        return in_array($role, ['staff', 'admin']);
    }
    
    public function isAdmin($user_id = null) {
        $user_id = $user_id ?? $_SESSION['user_id'] ?? null;
        if (!$user_id) return false;
        
        $role = $_SESSION['role'] ?? null;
        return $role === 'admin';
    }
    
    public function isStudent($user_id = null) {
        $user_id = $user_id ?? $_SESSION['user_id'] ?? null;
        if (!$user_id) return false;
        
        $role = $_SESSION['role'] ?? null;
        return $role === 'student';
    }
    
    public function canEditUser($targetUserId, $currentUserId = null) {
        $currentUserId = $currentUserId ?? $_SESSION['user_id'] ?? null;
        if (!$currentUserId) return false;
        
        $currentRole = $_SESSION['role'] ?? null;
        
        // Get target user's role
        $stmt = $this->pdo->prepare("SELECT role FROM users WHERE user_id = ?");
        $stmt->execute([$targetUserId]);
        $targetUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$targetUser) return false;
        
        $targetRole = $targetUser['role'];
        
        // Admin can edit anyone except themselves
        if ($currentRole === 'admin' && $currentUserId != $targetUserId) {
            return true;
        }
        
        // Staff can only edit students
        if ($currentRole === 'staff' && $targetRole === 'student') {
            return true;
        }
        
        return false;
    }
    
    public function requireLogin($redirect_url = 'login.php') {
        if (!$this->isLoggedIn()) {
            header("Location: $redirect_url");
            exit;
        }
    }
    
    public function requireStaff($redirect_url = 'index.php') {
        $this->requireLogin();
        if (!$this->isStaff()) {
            header("Location: $redirect_url?error=access_denied");
            exit;
        }
    }
}

function requireAuth($role = 'user') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?error=login_required");
        exit;
    }
    
    if ($role === 'staff') {
        $userRole = $_SESSION['role'] ?? '';
        if (!in_array($userRole, ['staff', 'admin'])) {
            header("Location: index.php?error=access_denied");
            exit;
        }
    }
}
?>