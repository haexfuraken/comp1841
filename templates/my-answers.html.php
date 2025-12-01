<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Answers - Student Q&A System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background-color: #f8f9fa;
        padding-top: 76px;
        padding-bottom: 60px;
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

    
    .module-badge {
        font-size: 0.8rem;
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

        <?php if (!empty($answers)): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold mb-0"><?= count($answers) ?></h3>
                        <p class="text-muted mb-0">Total Answers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold mb-0"><?= count(array_filter($answers, function($a) { return $a['is_solution']; })) ?></h3>
                        <p class="text-muted mb-0">Accepted Solutions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold mb-0"><?= count(array_unique(array_column($answers, 'question_id'))) ?></h3>
                        <p class="text-muted mb-0">Questions Helped</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="fw-bold mb-0"><?= !empty($answers) ? round((count(array_filter($answers, function($a) { return $a['is_solution']; })) / count($answers)) * 100) : 0 ?>%</h3>
                        <p class="text-muted mb-0">Solution Rate</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h5 class="mb-4 fw-bold">Your Answers</h5>

                <?php foreach ($answers as $answer): ?>
                <div class="card answer-card" style="cursor: pointer;" onclick="window.location.href='view-question.php?id=<?= $answer['question_id'] ?>#answer-<?= $answer['answer_id'] ?>'">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($answer['module_code'])): ?>
                                <span class="badge bg-primary module-badge">
                                    <?= htmlspecialchars($answer['module_code']) ?>
                                </span>
                                <small class="text-muted">
                                    <?= htmlspecialchars($answer['module_name']) ?>
                                </small>
                                <?php else: ?>
                                <span class="badge bg-secondary module-badge">
                                    General
                                </span>
                                <small class="text-muted">
                                    No module specified
                                </small>
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
                                <?= htmlspecialchars($answer['question_title']) ?>
                            </a>
                        </h5>

                        <div class="answer-content mb-3">
                            <div class="text-dark"><?= parseMarkdown($answer['content']) ?></div>
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
                        <i class="bi bi-chat-left-text"></i>
                        <h4 class="text-muted mb-3">No Answers Yet</h4>
                        <p class="text-muted mb-4">
                            You haven't answered any questions yet. Start helping the community by sharing your
                            knowledge!
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    
    function parseMarkdown(text) {
        if (!text) return '';

        
        const codeBlocks = [];
        let placeholder = 'CODEBLOCK_PLACEHOLDER_';

        
        text = text.replace(/```([\s\S]*?)```/g, function(match, code) {
            const index = codeBlocks.length;
            codeBlocks.push(code.trim());
            return placeholder + index + '_END';
        });

        
        text = text.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');

        
        text = text.replace(/\n\s*\n/g, '</p><p>');
        text = text.replace(/\n/g, '<br>');

        
        if (!text.startsWith('<p>')) {
            text = '<p>' + text + '</p>';
        }

        
        text = text.replace(new RegExp(placeholder + '(\\d+)_END', 'g'), function(match, index) {
            return '<pre class="bg-light p-3 rounded mt-2 mb-2"><code>' +
                codeBlocks[parseInt(index)].replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                '</code></pre>';
        });

        return text;
    }
    </script>
</body>

</html>