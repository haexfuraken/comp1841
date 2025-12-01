<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile & Settings - COMP1841 Q&A System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            padding-top: 80px;
        }
        .settings-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .profile-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background: #343a40;
            color: white;
            text-align: center;
            padding: 2rem;
        }
        .settings-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .nav-pills .nav-link {
            border-radius: 25px;
            margin-bottom: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            background: transparent;
            color: #6c757d;
            transition: all 0.3s ease;
        }
        .nav-pills .nav-link:hover {
            background-color: rgba(52, 58, 64, 0.1);
            color: #343a40;
            transform: translateX(5px);
        }
        .nav-pills .nav-link.active {
            background: #343a40;
            color: white;
        }
        .btn-primary {
            background: #343a40;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
        }
        .btn-primary:hover {
            background: #495057;
            transform: translateY(-1px);
        }
        .form-control:focus {
            border-color: #343a40;
            box-shadow: 0 0 0 0.25rem rgba(52, 58, 64, 0.25);
        }
        .profile-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .tab-content {
            padding: 2rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <div class="settings-container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="profile-card">
                        <i class="bi bi-person-circle profile-icon"></i>
                        <h4 class="fw-bold mb-2"><?= htmlspecialchars($currentUser['full_name']) ?></h4>
                        <p class="mb-2 opacity-75"><?= htmlspecialchars($currentUser['email']) ?></p>
                        <span class="badge bg-light text-dark"><?= ucfirst($currentUser['role']) ?></span>
                    </div>

                    <div class="nav flex-column nav-pills mt-4" role="tablist">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#profile-tab" 
                                type="button" role="tab">
                            <i class="bi bi-person me-2"></i>Profile Information
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#password-tab" 
                                type="button" role="tab">
                            <i class="bi bi-lock me-2"></i>Change Password
                        </button>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card settings-card">
                        <?php if (!empty($message)): ?>
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="profile-tab" role="tabpanel">
                                <h5 class="mb-4">Update Profile Information</h5>
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_profile">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="full_name" class="form-label fw-semibold">Full Name</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                                   value="<?= htmlspecialchars($currentUser['full_name']) ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label fw-semibold">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?= htmlspecialchars($currentUser['email']) ?>" required>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Update Profile
                                    </button>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="password-tab" role="tabpanel">
                                <h5 class="mb-4">Change Password</h5>
                                <form method="POST">
                                    <input type="hidden" name="action" value="change_password">
                                    
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label fw-semibold">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" 
                                               name="current_password" required>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="new_password" class="form-label fw-semibold">New Password</label>
                                            <input type="password" class="form-control" id="new_password" 
                                                   name="new_password" required>
                                            <div class="form-text">Minimum 6 characters</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="confirm_new_password" class="form-label fw-semibold">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirm_new_password" 
                                                   name="confirm_new_password" required>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-key me-2"></i>Change Password
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>