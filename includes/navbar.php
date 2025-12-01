<?php
if (!isset($auth)) {
    require_once __DIR__ . '/auth.php';
}

$isLoggedIn = $auth->isLoggedIn();
$currentUser = $isLoggedIn ? $auth->getCurrentUser() : null;
?>
<nav class="navbar navbar-expand navbar-dark bg-dark fixed-top" role="navigation" aria-label="Main navigation">
    <style>
        body {
            padding-top: 76px;
        }

        .nav-item .btn-outline-light:hover {
            color: #000 !important;
            background-color: #f8f9fa !important;
            border-color: #f8f9fa !important;
        }
        
        .nav-item .btn-outline-light:focus,
        .nav-item .btn-outline-light:active,
        .nav-item .btn-outline-light.show {
            color: #000 !important;
            background-color: #f8f9fa !important;
            border-color: #f8f9fa !important;
            box-shadow: 0 0 0 0.25rem rgba(248, 249, 250, 0.5) !important;
        }

        /* Enhanced Dropdown Styling */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            min-width: 250px;
            padding: 0;
            margin-top: 0.5rem;
            z-index: 9999 !important;
        }

        .dropdown-header {
            padding: 0.75rem 1rem !important;
            background: #343a40 !important;
            color: white !important;
            border-radius: 8px 8px 0 0;
            margin: -0.5rem 0rem 0.5rem 0rem;
        }

        .dropdown-item {
            padding: 12px 20px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .dropdown-item.text-danger:hover {
            background-color: #f8d7da;
            color: #721c24 !important;
            border-radius: 0 0 8px 8px;
        }

        .dropdown-item i {
            width: 18px;
            font-size: 0.9rem;
        }

        .navbar-nav .dropdown-toggle::after {
            margin-left: 0.5rem;
        }

        /* User badge styling */
        .dropdown-header small {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        /* Navbar dropdown positioning */
        .navbar .dropdown {
            position: relative;
        }

        .navbar .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            left: auto !important;
            right: 0 !important;
        }

        .dropdown.show .dropdown-menu {
            display: block !important;
        }
    </style>
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php" aria-label="Student Q&A System Home">Student Q&A</a>
        
        <ul class="navbar-nav ms-auto d-flex flex-row gap-3">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <?php if ($isLoggedIn): ?>
                    <!-- Authenticated User Navigation -->

                    <?php if ($auth->isStaff()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="staffDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Staff Area
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="manage-users.php">
                                Manage Users
                            </a></li>
                            <li><a class="dropdown-item" href="manage-modules.php">
                                Manage Modules
                            </a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Admin</a>
                    </li>
                    <!-- Enhanced User Account Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-outline-light btn-sm px-3" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" 
                           style="cursor: pointer;" tabindex="0">
                            Profile
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><h6 class="dropdown-header">
                                <?= htmlspecialchars($currentUser['full_name']) ?>
                                <br><small class="text-muted"><?= ucfirst($currentUser['role']) ?> • <?= htmlspecialchars($currentUser['email']) ?></small>
                            </h6></li>
                            <li><a class="dropdown-item" href="my-questions.php">
                                <i class="bi bi-question-circle me-2"></i> My Questions
                            </a></li>
                            <li><a class="dropdown-item" href="my-answers.php">
                                <i class="bi bi-chat-left-text me-2"></i> My Answers
                            </a></li>
                            <li><a class="dropdown-item" href="settings.php">
                                <i class="bi bi-gear me-2"></i> Profile & Settings
                            </a></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Guest User Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light btn-sm ms-2" href="register.php">
                            <i class="bi bi-person-plus"></i> Sign Up
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
    </div>
</nav>