<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Activity - <?= htmlspecialchars($targetUser['username']) ?> - Student Q&A System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
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
        border-radius: 12px;
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

    
    .answer-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
    }

    .answer-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .answer-content {
        max-height: 300px;
        overflow: hidden;
        position: relative;
    }

    .answer-content.expanded {
        max-height: none;
    }

    .answer-stats {
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

    
    .stats-card {
        background: #6c757d;
        color: white;
        border-radius: 12px;
    }

    .stats-card .card-body {
        padding: 1.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .stat-block {
        text-align: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
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

    .alert {
        border: none;
        border-radius: 8px;
    }

    
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
    }

    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        font-weight: 500;
        padding: 1rem 1.5rem;
        margin-bottom: -2px;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: transparent;
    }

    .nav-tabs .nav-link:hover:not(.active) {
        color: #495057;
        border-bottom-color: #dee2e6;
    }

    .tab-content {
        padding: 2rem 0;
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h1 class="h2 fw-bold mb-1">
                    User Activity: <?= htmlspecialchars($targetUser['username']) ?>
                </h1>
                <p class="text-muted mb-0">View and manage all questions and answers from this user</p>
            </div>
            <div>
                <a href="manage-users.php" class="btn btn-dark">
                    Back to Users
                </a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <div><?= htmlspecialchars($message) ?></div>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Question Stats</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0"><?= count($questions) ?></h3>
                                <p class="text-muted mb-0 small">Total Questions</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0"><?= array_sum(array_column($questions, 'answer_count')) ?></h3>
                                <p class="text-muted mb-0 small">Total Answers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0"><?= count(array_filter($questions, function($q) { return $q['answer_count'] > 0; })) ?></h3>
                                <p class="text-muted mb-0 small">Answered</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-3">Answer Stats</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0"><?= count($answers) ?></h3>
                                <p class="text-muted mb-0 small">Total Answers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0"><?= count(array_filter($answers, function($a) { return $a['is_solution']; })) ?></h3>
                                <p class="text-muted mb-0 small">Accepted</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="fw-bold mb-0"><?= count(array_unique(array_column($answers, 'question_id'))) ?></h3>
                                <p class="text-muted mb-0 small">Questions Helped</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <ul class="nav nav-tabs" id="activityTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions" 
                        type="button" role="tab" aria-controls="questions" aria-selected="true">
                    <i class="bi bi-question-circle me-1"></i> Questions (<?= count($questions) ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="answers-tab" data-bs-toggle="tab" data-bs-target="#answers" 
                        type="button" role="tab" aria-controls="answers" aria-selected="false">
                    <i class="bi bi-chat-left-text me-1"></i> Answers (<?= count($answers) ?>)
                </button>
            </li>
        </ul>

        
        <div class="tab-content" id="activityTabsContent">
            
            <div class="tab-pane fade show active" id="questions" role="tabpanel" aria-labelledby="questions-tab">
                <?php if (!empty($questions)): ?>
                    <?php foreach ($questions as $question): ?>
                    <div class="card question-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <?php if (!empty($question['module_code'])): ?>
                                    <span class="badge bg-primary module-badge">
                                        <?= htmlspecialchars($question['module_code']) ?>
                                    </span>
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
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-question-circle"></i>
                        <h4 class="text-muted mb-3">No Questions</h4>
                        <p class="text-muted">This user hasn't asked any questions yet.</p>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="tab-pane fade" id="answers" role="tabpanel" aria-labelledby="answers-tab">
                <?php if (!empty($answers)): ?>
                    <?php foreach ($answers as $answer): ?>
                    <div class="card answer-card" style="cursor: pointer;" onclick="window.location.href='view-question.php?id=<?= $answer['question_id'] ?>'">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($answer['module_code'])): ?>
                                    <span class="badge bg-primary module-badge">
                                        Module Code Available
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary module-badge">
                                        General
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end">
                                    <?php if ($answer['is_solution']): ?>
                                    <span class="badge bg-success mb-2">
                                        <i class="bi bi-check-circle"></i> Accepted Solution
                                    </span><br>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <?= date('M j, Y g:i A', strtotime($answer['created_at'])) ?>
                                    </small>
                                </div>
                            </div>

                            <h5 class="card-title fw-bold text-dark mb-2">
                                <a href="view-question.php?id=<?= $answer['question_id'] ?>"
                                    class="text-decoration-none text-dark">
                                    Answer to: <?= htmlspecialchars($answer['question_title']) ?>
                                </a>
                            </h5>

                            <div class="answer-content mb-3">
                                <div class="text-dark">
                                    <?= htmlspecialchars(substr(strip_tags($answer['content']), 0, 300)) ?>
                                    <?= strlen(strip_tags($answer['content'])) > 300 ? '...' : '' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-chat-left-text"></i>
                        <h4 class="text-muted mb-3">No Answers</h4>
                        <p class="text-muted">This user hasn't provided any answers yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>