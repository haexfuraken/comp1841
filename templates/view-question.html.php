<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $question ? htmlspecialchars($question['title']) : 'Question Not Found' ?> - Student Q&A System</title>
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

    .question-card {
        border-left: 4px solid #0d6efd;
    }

    .comment-card {
        border-left: 3px solid #dee2e6;
    }

    .solution-comment {
        border-left-color: #198754;
        background-color: #f8fff9;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        font-size: 2rem;
        color: #6c757d;
    }

    .question-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }

    
    body {
        padding-top: 0;
        
    }

    
    .main-content {
        margin-top: 120px !important;
        margin-bottom: 50px !important;
        padding-left: 8rem;
        padding-right: 8rem;
    }

    
    .question-container,
    .comments-container {
        max-width: 100%;
        width: 100%;
    }

    
    .question-card {
        margin-bottom: 3rem;
    }

    .comments-container {
        margin-top: 0;
    }

    
    .container-fluid {
        max-width: 1920px;
        margin: 0 auto;
    }

    
    .vote-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 60px;
        padding: 0.5rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-right: 1rem;
    }

    .vote-btn {
        border: none;
        background: none;
        color: #6c757d;
        font-size: 1.8rem;
        padding: 0.5rem;
        cursor: pointer;
        transition: color 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .vote-btn:hover {
        color: #007bff;
        text-decoration: none;
    }

    .vote-btn.upvoted {
        color: #28a745;
    }

    .vote-btn.downvoted {
        color: #dc3545;
    }

    .vote-count {
        font-weight: bold;
        margin: 0.25rem 0;
        color: #495057;
    }

    
    .btn-outline-light:hover {
        color: #212529 !important;
        
        background-color: #f8f9fa;
        border-color: #f8f9fa;
    }

    
    pre {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 1rem;
        margin: 1rem 0;
        font-family: ui-monospace, monospace;
        font-size: 0.9rem;
        font-weight: 400;
        color: #000000;
        line-height: 1.1;
        overflow-x: auto;
        white-space: pre;
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container-fluid main-content">
        
        <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if ($question): ?>
        
        <div class="row">
            <div class="col-12">
                <div class="card question-card question-container mb-4">
                    <div class="card-body">
                        <div class="d-flex">
                            
                            <div class="vote-section me-3">
                                <?php 
                                $questionUserVote = $userVotes['questions'][$question['question_id']] ?? null;
                                $questionNetVotes = $question['net_votes'] ?? (($question['upvotes'] ?? 0) - ($question['downvotes'] ?? 0));
                                ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="vote_action" value="1">
                                    <input type="hidden" name="vote_type" value="upvote">
                                    <input type="hidden" name="target_type" value="question">
                                    <input type="hidden" name="target_id" value="<?= $question['question_id'] ?>">
                                    <?php if ($isLoggedIn): ?>
                                    <button type="submit"
                                        class="vote-btn <?= $questionUserVote === 'upvote' ? 'upvoted' : '' ?>">
                                        <i class="bi bi-caret-up-fill"></i>
                                    </button>
                                    <?php else: ?>
                                    <a href="login.php?msg=login_required" class="vote-btn">
                                        <i class="bi bi-caret-up-fill"></i>
                                    </a>
                                    <?php endif; ?>
                                </form>
                                <span class="vote-count"><?= $questionNetVotes ?></span>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="vote_action" value="1">
                                    <input type="hidden" name="vote_type" value="downvote">
                                    <input type="hidden" name="target_type" value="question">
                                    <input type="hidden" name="target_id" value="<?= $question['question_id'] ?>">
                                    <?php if ($isLoggedIn): ?>
                                    <button type="submit"
                                        class="vote-btn <?= $questionUserVote === 'downvote' ? 'downvoted' : '' ?>">
                                        <i class="bi bi-caret-down-fill"></i>
                                    </button>
                                    <?php else: ?>
                                    <a href="login.php?msg=login_required" class="vote-btn">
                                        <i class="bi bi-caret-down-fill"></i>
                                    </a>
                                    <?php endif; ?>
                                </form>
                            </div>

                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-end mb-2">
                                    <small class="question-meta">
                                        <?= date('M j, Y \a\t g:i A', strtotime($question['created_at'])) ?>
                                    </small>
                                </div>
                                
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
                                        <small class="text-muted">
                                            No module specified
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if ($question['is_answered']): ?>
                                        <span class="badge bg-success">Answered</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                
                                <div id="question-title-view">
                                    <h2 class="fw-bold text-dark mb-3"><?= htmlspecialchars($question['title']) ?></h2>
                                </div>

                                
                                <div class="mb-4">
                                    <div id="question-content-view">
                                        <div class="text-dark"><?= parseMarkdown($question['content']) ?></div>
                                    </div>

                                    
                                    <?php if (!empty($question['image_path']) && file_exists($question['image_path'])): ?>
                                    <div class="mt-3">
                                        <img src="<?= htmlspecialchars($question['image_path']) ?>"
                                            class="img-fluid rounded shadow-sm" alt="Question screenshot"
                                            style="max-height: 400px;">
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex align-items-start mb-3">
                                    <i class="bi bi-person-circle profile-icon user-avatar me-2" style="font-size: 2.5rem;"></i>
                                    <div class="lh-sm" style="margin-top: 0.5rem;">
                                        <strong><?= htmlspecialchars($question['full_name']) ?></strong>
                                        <br>
                                        <small class="question-meta">
                                            @<?= htmlspecialchars($question['username']) ?>
                                        </small>
                                    </div>
                                </div>

                                
                                <div class="d-flex align-items-center justify-content-end">

                                    
                                    <?php if ($isLoggedIn && $auth->ownsQuestion($question['question_id'])): ?>
                                    <div class="d-flex gap-2">
                                        <a href="edit-question.php?id=<?= $question['question_id'] ?>"
                                            class="btn btn-sm btn-secondary px-3">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="delete-question.php?id=<?= $question['question_id'] ?>"
                                            class="btn btn-sm btn-danger px-3"
                                            onclick="return confirm('Are you sure you want to delete this question?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </div>
                                    <?php elseif ($isLoggedIn && ($currentUser['role'] === 'staff' || $currentUser['role'] === 'admin')): ?>
                                    <div class="d-flex gap-2 align-items-center">
                                        
                                        <button type="button" class="btn btn-sm btn-outline-primary px-3" 
                                                onclick="openModuleModal()"
                                                title="Assign/change module">
                                            <i class="bi bi-collection"></i> Assign Module
                                        </button>
                                        
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this question? This action cannot be undone.')">
                                            <input type="hidden" name="delete_question" value="1">
                                            <input type="hidden" name="question_id" value="<?= $question['question_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger px-3">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card comments-container">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <?= count($answers) ?> Answer(s)
                                </h5>
                            </div>
                            <div class="card-body">
                                
                                <?php if ($isLoggedIn): ?>
                                <form method="POST" class="mb-4">
                                    <div class="mb-3">
                                        <label for="comment" class="form-label fw-semibold">Add Your Answer</label>
                                        <textarea class="form-control" id="comment" name="comment" rows="4"
                                            placeholder="Share your knowledge..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Post Answer</button>
                                </form>
                                <hr>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    Please <a href="login.php">sign in</a> to post answers.
                                </div>
                                <hr>
                                <?php endif; ?>

                                
                                <?php if (!empty($answers)): ?>
                                <?php foreach ($answers as $answer): ?>
                                <div
                                    class="comment-card card mb-3 <?= $answer['is_solution'] ? 'solution-comment' : '' ?>">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            
                                            <div class="vote-section me-3">
                                                <?php 
                                            $answerUserVote = $userVotes['answers'][$answer['answer_id']] ?? null;
                                            $answerNetVotes = $answer['net_votes'] ?? 0;
                                            ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="vote_action" value="1">
                                                    <input type="hidden" name="vote_type" value="upvote">
                                                    <input type="hidden" name="target_type" value="answer">
                                                    <input type="hidden" name="target_id"
                                                        value="<?= $answer['answer_id'] ?>">
                                                    <?php if ($isLoggedIn): ?>
                                                    <button type="submit"
                                                        class="vote-btn <?= $answerUserVote === 'upvote' ? 'upvoted' : '' ?>">
                                                        <i class="bi bi-caret-up-fill"></i>
                                                    </button>
                                                    <?php else: ?>
                                                    <a href="login.php?msg=login_required" class="vote-btn">
                                                        <i class="bi bi-caret-up-fill"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </form>
                                                <span class="vote-count"><?= $answerNetVotes ?></span>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="vote_action" value="1">
                                                    <input type="hidden" name="vote_type" value="downvote">
                                                    <input type="hidden" name="target_type" value="answer">
                                                    <input type="hidden" name="target_id"
                                                        value="<?= $answer['answer_id'] ?>">
                                                    <?php if ($isLoggedIn): ?>
                                                    <button type="submit"
                                                        class="vote-btn <?= $answerUserVote === 'downvote' ? 'downvoted' : '' ?>">
                                                        <i class="bi bi-caret-down-fill"></i>
                                                    </button>
                                                    <?php else: ?>
                                                    <a href="login.php?msg=login_required" class="vote-btn">
                                                        <i class="bi bi-caret-down-fill"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </form>
                                            </div>

                                            
                                            <?php if ($isLoggedIn && $question['user_id'] == $currentUser['user_id']): ?>
                                            <div class="accept-section me-3">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="toggle_solution" value="1">
                                                    <input type="hidden" name="answer_id" value="<?= $answer['answer_id'] ?>">
                                                    <button type="submit" 
                                                        class="btn btn-sm <?= $answer['is_solution'] ? 'btn-success' : 'btn-outline-success' ?>"
                                                        style="width: 90px;"
                                                        title="<?= $answer['is_solution'] ? 'Unaccept this answer' : 'Accept this answer as the solution' ?>">
                                                        <i class="bi bi-check-circle<?= $answer['is_solution'] ? '-fill' : '' ?>"></i>
                                                        <?= $answer['is_solution'] ? 'Accepted' : 'Accept' ?>
                                                    </button>
                                                </form>
                                            </div>
                                            <?php endif; ?>

                                            
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-end mb-2">
                                                    <small class="question-meta">
                                                        <?= date('M j, Y \a\t g:i A', strtotime($answer['created_at'])) ?>
                                                    </small>
                                                </div>
                                                
                                                <?php if ($answer['is_solution']): ?>
                                                <div class="mb-2">
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Accepted Answer
                                                    </span>
                                                </div>
                                                <?php endif; ?>

                                                
                                                <div id="answer-content-view-<?= $answer['answer_id'] ?>" class="mb-3">
                                                    <?= parseMarkdown($answer['content']) ?>
                                                </div>

                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="bi bi-person-circle profile-icon me-2" style="font-size: 2.5rem; color: #6c757d;"></i>
                                                    <div class="lh-sm">
                                                        <strong><?= htmlspecialchars($answer['full_name']) ?></strong>
                                                        <br>
                                                        <small class="question-meta">
                                                            @<?= htmlspecialchars($answer['username']) ?>
                                                        </small>
                                                    </div>
                                                </div>

                                                <?php if ($isLoggedIn && ($auth->ownsAnswer($answer['answer_id']) || $auth->isStaff())): ?>
                                                <div class="d-flex justify-content-end">
                                                    <div>
                                                        <a href="edit-answer.php?id=<?= $answer['answer_id'] ?>&question_id=<?= $question['question_id'] ?>"
                                                            class="btn btn-sm btn-secondary me-1 px-3">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                        <a href="delete-answer.php?id=<?= $answer['answer_id'] ?>&question_id=<?= $question['question_id'] ?>"
                                                            class="btn btn-sm btn-danger px-3"
                                                            onclick="return confirm('Are you sure you want to delete this answer?')">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                
                <div class="text-center py-5">
                    <i class="bi bi-question-circle text-muted" style="font-size: 4rem;"></i>
                    <h3 class="text-muted mt-3">Question Not Found</h3>
                    <p class="text-muted">The question you're looking for doesn't exist or is no longer available.</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left"></i> Back to Questions
                    </a>
                </div>
                <?php endif; ?>
            </div>

            
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
                crossorigin="anonymous"></script>

            <script>
            
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            
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
                    return '<pre class="code-block bg-light p-3 rounded mt-2 mb-2"><code>' +
                        codeBlocks[parseInt(index)].replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                        '</code></pre>';
                });

                return text;
            }
            
            
            function openModuleModal() {
                
                const modalElement = document.getElementById('moduleAssignModal');
                if (modalElement) {
                    
                    if (window.bootstrap && bootstrap.Modal) {
                        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modal.show();
                    } 
                    
                    else if (modalElement.setAttribute) {
                        modalElement.classList.add('show');
                        modalElement.style.display = 'block';
                        document.body.classList.add('modal-open');
                        
                        
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        backdrop.onclick = function() {
                            closeModuleModal();
                        };
                        document.body.appendChild(backdrop);
                    }
                }
            }
            
            function closeModuleModal() {
                const modalElement = document.getElementById('moduleAssignModal');
                const backdrop = document.querySelector('.modal-backdrop');
                
                if (modalElement) {
                    modalElement.classList.remove('show');
                    modalElement.style.display = 'none';
                }
                
                if (backdrop) {
                    backdrop.remove();
                }
                
                document.body.classList.remove('modal-open');
            }
            </script>

<?php if ($isLoggedIn && ($currentUser['role'] === 'staff' || $currentUser['role'] === 'admin')): ?>
<div class="modal fade" id="moduleAssignModal" tabindex="-1" aria-labelledby="moduleAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moduleAssignModalLabel">
                    <i class="bi bi-collection"></i> Assign Module
                </h5>
                <button type="button" class="btn-close" onclick="closeModuleModal()" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="assign_module" value="1">
                    <input type="hidden" name="question_id" value="<?= $question['question_id'] ?>">
                    
                    <div class="mb-3">
                        <label for="module_select" class="form-label">Select Module</label>
                        <select name="new_module_id" id="module_select" class="form-select" required>
                            <option value="">No Module</option>
                            <?php foreach ($allModules as $module): ?>
                                <option value="<?= $module['module_id'] ?>" 
                                        <?= (isset($question['module_id']) && $question['module_id'] == $module['module_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($module['module_code']) ?> - <?= htmlspecialchars($module['module_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle"></i>
                            This will change the module assignment for this question. Select "No Module" to remove the current assignment.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModuleModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Assign Module
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

</body>

</html>