<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask a Question - Student Q&A System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-kerning: auto;
        font-size: 16px;
        font-stretch: normal;
        font-style: normal;
        font-variant: normal;
        font-variant-ligatures: normal;
        font-weight: normal;
        background-color: #f8f9fa;
    }

    .dropdown-menu {
        border: 0;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        padding: 0.5rem 0;
        min-width: 250px;
    }

    .dropdown-header {
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        border-radius: 8px 8px 0 0;
        margin: -0.5rem -0rem 0.5rem -0rem;
    }

    .dropdown-item {
        padding: 0.6rem 1rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        padding-left: 1.2rem;
    }

    .dropdown-item.text-danger:hover {
        background-color: #f8d7da;
        color: #721c24 !important;
    }

    .dropdown-item i {
        width: 18px;
        font-size: 0.9rem;
    }

    .navbar-nav .dropdown-toggle::after {
        margin-left: 0.5rem;
    }

    .dropdown-header small {
        font-size: 0.75rem;
        opacity: 0.9;
    }

    .markdown-content {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
        line-height: 1.6;
        color: #333;
    }

    .markdown-content pre {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 12px;
        overflow-x: auto;
        margin: 1rem 0;
    }

    .markdown-content code {
        background-color: #f1f3f4;
        padding: 2px 4px;
        border-radius: 3px;
        font-family: 'Monaco', 'Consolas', 'Courier New', monospace;
        font-size: 0.875em;
    }

    .markdown-content pre code {
        background-color: transparent;
        padding: 0;
    }

    .markdown-content blockquote {
        border-left: 4px solid #dee2e6;
        margin: 1rem 0;
        padding-left: 1rem;
        color: #6c757d;
    }

    .markdown-content h1,
    .markdown-content h2,
    .markdown-content h3 {
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .markdown-content ul,
    .markdown-content ol {
        padding-left: 1.5rem;
        margin: 1rem 0;
    }

    .markdown-content li {
        margin-bottom: 0.25rem;
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <h2 class="text-center mb-4 fw-bold">Ask a Question</h2>

                <?php if (!empty($message)): ?>
                <div class="alert alert-success text-center mb-4" role="alert">
                    <strong>Success!</strong> <?= htmlspecialchars($message) ?>
                    <br><small class="text-muted">Redirecting to home page in <span id="countdown">3</span>
                        seconds...</small>
                </div>
                <script>
                let countdown = 3;
                const countdownElement = document.getElementById('countdown');
                const timer = setInterval(() => {
                    countdown--;
                    countdownElement.textContent = countdown;
                    if (countdown <= 0) {
                        clearInterval(timer);
                        window.location.href = 'index.php';
                    }
                }, 1000);
                </script>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center mb-4" role="alert">
                    <strong>Error!</strong> <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold">Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" maxlength="200"
                                    placeholder="Maximum 200 characters"
                                    value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label fw-semibold">
                                    Details <span class="text-danger">*</span>
                                </label>

                                <textarea class="form-control" id="content" name="content" rows="5" 
                                          placeholder="Write a clear and detailed description of your question" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="module_id" class="form-label fw-semibold">Related Module (Optional)</label>
                                <select class="form-select" id="module_id" name="module_id">
                                    <option value="">Choose the module this question relates to (optional)</option>
                                    <?php foreach ($modules as $module): ?>
                                    <option value="<?= htmlspecialchars($module['module_id']) ?>"
                                        <?= (isset($_POST['module_id']) && $_POST['module_id'] == $module['module_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($module['module_code']) ?> -
                                        <?= htmlspecialchars($module['module_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="image" class="form-label fw-semibold">Screenshot/Image (Optional)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*"
                                    placeholder="Upload a screenshot or image to help illustrate your question. Supported formats: JPG, JPEG, PNG, GIF">
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    Post Question
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            const dropdownElements = document.querySelectorAll('.dropdown-toggle');
            dropdownElements.forEach(function(element) {
                if (!element.hasAttribute('data-bs-initialized')) {
                    new bootstrap.Dropdown(element);
                    element.setAttribute('data-bs-initialized', 'true');
                }
            });
        }
    });

    function insertMarkdown(prefix, suffix = '') {
        const textarea = document.getElementById('content');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const selectedText = textarea.value.substring(start, end);

        const replacement = prefix + selectedText + suffix;

        textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);

        const newPosition = start + prefix.length + selectedText.length;
        textarea.focus();
        textarea.setSelectionRange(newPosition, newPosition);
    }
    </script>
</body>

</html>