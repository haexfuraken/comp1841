<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Q&A System</title>
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
        transition: transform 0.2s;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .question-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
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

    
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
    }

    .module-badge {
        font-size: 0.8rem;
    }

    .question-meta {
        font-size: 0.9rem;
        color: #6c757d;
    }

    
    body {
        padding-top: 56px;
        
        margin: 0;
        
    }

    
    .main-content {
        margin-top: 0;
        
    }

    
    .navbar {
        margin-bottom: 0 !important;
    }

    
    .btn-outline-light:hover {
        color: #212529 !important;
        
        background-color: #f8f9fa;
        border-color: #f8f9fa;
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search questions..."
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <?php if (!empty($_GET['module'])): ?>
                        <input type="hidden" name="module" value="<?= htmlspecialchars($_GET['module']) ?>">
                        <?php endif; ?>
                        <?php if (!empty($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container my-5 main-content">
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

        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <?php
                        $totalQuestions = count($questions);
                        $questionsPerPage = 15;
                        $currentPage = (int)($_GET['page'] ?? 1);
                        $totalPages = ceil($totalQuestions / $questionsPerPage);
                        $offset = ($currentPage - 1) * $questionsPerPage;
                        $displayQuestions = array_slice($questions, $offset, $questionsPerPage);
                        ?>
                        <h3 class="fw-bold mb-0"><?= $totalQuestions ?> Questions</h3>
                        <small class="text-muted">
                            Showing
                            <?= min($totalQuestions, ($offset + 1)) ?>-<?= min($totalQuestions, ($offset + count($displayQuestions))) ?>
                            of <?= $totalQuestions ?>
                        </small>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <form method="GET" class="d-flex gap-2">
                            <?php if (!empty($_GET['search'])): ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
                            <?php endif; ?>

                            <select class="form-select form-select-sm" name="module" onchange="this.form.submit()"
                                style="width: 250px;">
                                <option value="">All Modules</option>
                                <?php 
                                
                                $moduleStmt = $pdo->prepare("SELECT DISTINCT module_code, module_name FROM modules ORDER BY module_code");
                                $moduleStmt->execute();
                                $modules = $moduleStmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($modules as $module): 
                                ?>
                                <option value="<?= $module['module_code'] ?>"
                                    <?= ($_GET['module'] ?? '') === $module['module_code'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($module['module_code']) ?> -
                                    <?= htmlspecialchars($module['module_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>

                            <select class="form-select form-select-sm" name="sort" onchange="this.form.submit()"
                                style="width: 150px;">
                                <option value="newest"
                                    <?= ($_GET['sort'] ?? 'newest') === 'newest' ? 'selected' : '' ?>>Newest First
                                </option>
                                <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>
                                    Oldest First</option>
                                <option value="most_voted"
                                    <?= ($_GET['sort'] ?? '') === 'most_voted' ? 'selected' : '' ?>>Most Voted</option>
                                <option value="most_answers"
                                    <?= ($_GET['sort'] ?? '') === 'most_answers' ? 'selected' : '' ?>>Most Answers
                                </option>
                            </select>

                            <?php if (!empty($_GET['module']) || !empty($_GET['sort']) || ($_GET['sort'] ?? 'newest') !== 'newest'): ?>
                            <a href="index.php<?= !empty($_GET['search']) ? '?search=' . urlencode($_GET['search']) : '' ?>"
                                class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-clockwise"></i>
                            </a>
                            <?php endif; ?>
                        </form>
                        <?php if ($isLoggedIn && !$auth->isStaff()): ?>
                        <a href="add-question.php" class="btn btn-primary btn-sm">
                            Ask Question
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($displayQuestions)): ?>
                <div class="row">
                    <?php foreach ($displayQuestions as $question): ?>
                    <div class="col-12 mb-4">
                        <div class="card question-card card" style="cursor: pointer;" onclick="window.location.href='view-question.php?id=<?= $question['question_id'] ?>'">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="vote-section me-3" onclick="event.stopPropagation();">
                                        <?php 
                                                $userVote = $userVotes['questions'][$question['question_id']] ?? null;
                                                $netVotes = $question['net_votes'] ?? ($question['upvotes'] - $question['downvotes']);
                                                ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="vote_action" value="1">
                                            <input type="hidden" name="vote_type" value="upvote">
                                            <input type="hidden" name="target_type" value="question">
                                            <input type="hidden" name="target_id"
                                                value="<?= $question['question_id'] ?>">
                                            <?php if ($isLoggedIn): ?>
                                            <button type="submit"
                                                class="vote-btn <?= $userVote === 'upvote' ? 'upvoted' : '' ?>">
                                                <i class="bi bi-caret-up-fill"></i>
                                            </button>
                                            <?php else: ?>
                                            <a href="login.php?msg=login_required" class="vote-btn">
                                                <i class="bi bi-caret-up-fill"></i>
                                            </a>
                                            <?php endif; ?>
                                        </form>
                                        <span class="vote-count"><?= $netVotes ?></span>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="vote_action" value="1">
                                            <input type="hidden" name="vote_type" value="downvote">
                                            <input type="hidden" name="target_type" value="question">
                                            <input type="hidden" name="target_id"
                                                value="<?= $question['question_id'] ?>">
                                            <?php if ($isLoggedIn): ?>
                                            <button type="submit"
                                                class="vote-btn <?= $userVote === 'downvote' ? 'downvoted' : '' ?>">
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
                                            <small class="question-meta">
                                                <?= timeAgo($question['created_at']) ?>
                                            </small>
                                        </div>
                                        <h5 class="card-title fw-bold text-dark mb-2">
                                            <a href="view-question.php?id=<?= $question['question_id'] ?>"
                                                class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($question['title']) ?>
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted mb-3">
                                            <?= createPreview($question['content'], 200) ?>
                                        </p>

                                        <div class="question-stats">
                                            <div class="stat-item">
                                                <i class="bi bi-chat-left-text"></i>
                                                <span><?= $question['answer_count'] ?? 0 ?> answers</span>
                                            </div>
                                            <div class="stat-item">
                                                <i class="bi bi-person-circle"></i>
                                                <span>by <?= htmlspecialchars($question['username']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="pagination-wrapper">
                    <div>
                        <small class="text-muted">
                            Page <?= $currentPage ?> of <?= $totalPages ?>
                            (<?= $totalQuestions ?> total questions)
                        </small>
                    </div>
                    <nav aria-label="Questions pagination">
                        <ul class="pagination mb-0">
                            <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=<?= $currentPage - 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['module']) ? '&module=' . urlencode($_GET['module']) : '' ?><?= !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '' ?>">
                                    <i class="bi bi-chevron-left"></i> Previous
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($totalPages, $currentPage + 2);
                                    
                                    if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=1<?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['module']) ? '&module=' . urlencode($_GET['module']) : '' ?><?= !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '' ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link"
                                    href="?page=<?= $i ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['module']) ? '&module=' . urlencode($_GET['module']) : '' ?><?= !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=<?= $totalPages ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['module']) ? '&module=' . urlencode($_GET['module']) : '' ?><?= !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '' ?>"><?= $totalPages ?></a>
                            </li>
                            <?php endif; ?>

                            <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link"
                                    href="?page=<?= $currentPage + 1 ?><?= !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?><?= !empty($_GET['module']) ? '&module=' . urlencode($_GET['module']) : '' ?><?= !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '' ?>">
                                    Next <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-question-circle" style="font-size: 4rem; color: #6c757d;"></i>
                    <h5 class="text-muted mt-3">No questions found</h5>
                    <?php if (!empty($_GET['search']) || !empty($_GET['module'])): ?>
                    <p class="text-muted">Try adjusting your search criteria or <a href="index.php">view all
                            questions</a>.</p>
                    <?php else: ?>
                    <p class="text-muted">Be the first to ask a question and help build our knowledge base!</p>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): ?>
                    <a href="add-question.php" class="btn btn-primary mt-3">
                        Ask the First Question
                    </a>
                    <?php else: ?>
                    <p class="text-muted mt-3">Please <a href="login.php">sign in</a> or <a href="register.php">create
                            an account</a> to ask questions.</p>
                    <div class="mt-3">
                        <a href="register.php" class="btn btn-primary me-2">
                            <i class="bi bi-person-plus"></i> Create Account
                        </a>
                        <a href="login.php" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Sign In
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>

    <script>
    function initializeDropdowns() {
        console.log('Attempting to initialize dropdowns...');

        const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
        const dropdownList = [...dropdownElementList].map(dropdownToggleEl => {
            try {
                return new bootstrap.Dropdown(dropdownToggleEl);
            } catch (e) {
                console.warn('Failed to initialize dropdown:', e);
                return null;
            }
        });

        console.log('Initialized', dropdownList.filter(d => d !== null).length, 'dropdowns');

        const userDropdown = document.getElementById('userDropdown');
        if (userDropdown) {
            console.log('Adding manual click handler for user dropdown');
            userDropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    const isVisible = dropdownMenu.classList.contains('show');

                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                    });

                    if (!isVisible) {
                        dropdownMenu.classList.add('show');
                        console.log('Manual dropdown opened');
                    }
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeDropdowns);
    } else {
        initializeDropdowns();
    }

    setTimeout(initializeDropdowns, 100);
    </script>
</body>

</html>