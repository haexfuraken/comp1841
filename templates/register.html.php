<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - COMP1841 Q&A System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background-color: #f8f9fa;
    }

    .register-container {
        min-height: 80vh;
        display: flex;
        align-items: center;
        padding: 2rem 0;
    }

    .register-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 600px;
        margin: 0 auto;
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
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="register-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card register-card">
                        <div class="card-body p-4">
                            <h2 class="fw-bold mb-4 text-center">Create Account</h2>

                            <?php if (!empty($message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php endif; ?>

                            <form method="POST" novalidate>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label fw-semibold">Username *</label>
                                        <input type="text" class="form-control" id="username" name="username"
                                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label fw-semibold">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="full_name" class="form-label fw-semibold">Full Name *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                        value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label fw-semibold">Password *</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            required>
                                        <div class="form-text">Minimum 6 characters</div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="confirm_password" class="form-label fw-semibold">Confirm Password
                                            *</label>
                                        <input type="password" class="form-control" id="confirm_password"
                                            name="confirm_password" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    <i class="bi bi-person-plus me-2"></i>Create Account
                                </button>

                                <div class="text-center">
                                    <p class="mb-0">Already have an account?
                                        <a href="login.php" class="text-decoration-none">Sign in here</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>