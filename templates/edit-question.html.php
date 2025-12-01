<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question - Student Q&A System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background-color: #f8f9fa;
        padding-top: 76px;
    }

    .edit-container {
        max-width: 600px;
        margin: 0 auto;
    }

    .edit-card {
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
        <div class="edit-container">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark">Edit Question</h2>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card edit-card">
                <div class="card-body p-4">
                    <form method="POST" novalidate>
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Title <span class="required">*</span></label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="<?= htmlspecialchars($question['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="module_id" class="form-label fw-semibold">Module</label>
                            <select class="form-select" id="module_id" name="module_id">
                                <option value="">Select Module (Optional)</option>
                                <?php foreach ($modules as $module): ?>
                                <option value="<?= $module['module_id'] ?>"
                                    <?= ($question['module_id'] ?? '') == $module['module_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($module['module_code'] . ' - ' . $module['module_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="content" class="form-label fw-semibold">Details <span class="required">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="8"
                                required><?= htmlspecialchars($question['content']) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="view-question.php?id=<?= $question['question_id'] ?>" class="btn btn-secondary"
                                onclick="return confirm('Are you sure you want to cancel? Any unsaved changes will be lost.');">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Are you sure you want to update this question?');">
                                Update Question
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