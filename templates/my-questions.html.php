<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Questions - Student Q&A System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background-color: #f8f9fa;
        padding-top: 76px;
        padding-bottom: 60px;
    }

    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    
    .question-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
    }

    .question-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .question-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1rem 0;
    }

    .question-stats {
        display: flex;
        gap: 1rem;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    
    .module-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    
    .navbar-nav .dropdown-toggle::after {
        margin-left: 0.5rem;
    }

    
    .nav-item .btn-outline-light {
        color: #ffffff !important;
        background-color: transparent !important;
        border-color: #ffffff;
    }

    .nav-item .btn-outline-light:not(:hover) {
        color: #ffffff !important;
        background-color: transparent !important;
    }

    .nav-item .btn-outline-light:hover {
        color: #000 !important;
        background-color: #f8f9fa !important;
        border-color: #f8f9fa !important;
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($questions)): ?>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold mb-0"><?= count($questions) ?></h3>
                        <p class="text-muted mb-0">Total Questions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold mb-0"><?= array_sum(array_column($questions, 'answer_count')) ?></h3>
                        <p class="text-muted mb-0">Total Answers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold mb-0"><?= count(array_filter($questions, function($q) { return $q['answer_count'] > 0; })) ?></h3>
                        <p class="text-muted mb-0">Answered Questions</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h5 class="mb-4 fw-bold">Your Questions</h5>

                <?php foreach ($questions as $question): ?>
                <div class="card question-card" style="cursor: pointer;" onclick="window.location.href='view-question.php?id=<?= $question['question_id'] ?>'">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($question['module_code'])): ?>
                                <span class="badge bg-primary module-badge">
                                    <?= htmlspecialchars($question['module_code']) ?>
                                </span>
                                <small class="text-muted">
                                    <?= htmlspecialchars($question['module_name']) ?>
                                </small>
                                <?php else: ?>
                                <span class="badge bg-secondary module-badge">
                                    General
                                </span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                <?= date('M j, Y g:i A', strtotime($question['created_at'])) ?>
                            </small>
                        </div>

                        <h5 class="card-title mb-3 fw-bold">
                            <a href="view-question.php?id=<?= $question['question_id'] ?>"
                                class="text-decoration-none text-dark">
                                <?= htmlspecialchars($question['title']) ?>
                            </a>
                        </h5>

                        <p class="card-text text-muted mb-3">
                            <?= htmlspecialchars(substr(strip_tags($question['content']), 0, 200)) ?>
                            <?= strlen(strip_tags($question['content'])) > 200 ? '...' : '' ?>
                        </p>

                        <?php if (!empty($question['image_path'])): ?>
                        <div class="mb-3">
                            <img src="<?= htmlspecialchars($question['image_path']) ?>" alt="Question image"
                                class="question-image">
                        </div>
                        <?php endif; ?>

                        <div class="question-stats">
                            <div class="stat-item">
                                <i class="bi bi-chat-left-text"></i>
                                <span><?= $question['answer_count'] ?>
                                    answer<?= $question['answer_count'] != 1 ? 's' : '' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body empty-state">
                        <i class="bi bi-question-circle"></i>
                        <h4 class="text-muted mb-3">No Questions Yet</h4>
                        <p class="text-muted mb-4">
                            You haven't asked any questions yet. Start by asking your first question to get help from
                            the community!
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>