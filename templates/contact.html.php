<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Admin - Student Q&A System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background-color: #f8f9fa;
        padding-top: 76px;
    }

    .contact-container {
        max-width: 600px;
        margin: 0 auto;
    }

    .contact-card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
    }

    .required {
        color: #dc3545;
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="contact-container">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">Contact Admin</h2>
            </div>

            <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card contact-card">
                <div class="card-body p-4">
                    <form method="POST" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-semibold">Your Name <span
                                        class="required">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?= htmlspecialchars($name ?? ($currentUser['full_name'] ?? '')) ?>"
                                    <?= $isLoggedIn ? 'readonly' : '' ?> required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">Email Address <span
                                        class="required">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($email ?? ($currentUser['email'] ?? '')) ?>"
                                    <?= $isLoggedIn ? 'readonly' : '' ?> required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold">Subject <span
                                    class="required">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                value="<?= htmlspecialchars($subject ?? '') ?>"
                                placeholder="Please be specific about your topic or issue (max 100 characters)" maxlength="100" required>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label fw-semibold">Message <span
                                    class="required">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="6"
                                placeholder="Please provide as much detail as possible to help us assist you better"
                                required><?= htmlspecialchars($messageContent ?? '') ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="mb-5 pb-4"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>