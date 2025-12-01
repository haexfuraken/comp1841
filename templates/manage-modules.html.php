<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Modules - Student Q&A System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        background-color: #f8f9fa;
        padding-top: 76px;
        padding-bottom: 60px;
    }

    
    .module-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
    }

    .module-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
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

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .modal-content {
        border-radius: 12px;
        border: none;
    }

    .modal-header {
        border-radius: 12px 12px 0 0;
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h1 class="h2 fw-bold mb-1">Module Management</h1>
                <p class="text-muted mb-0">Manage course modules and their information</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                    Add Module
                </button>
            </div>
        </div>
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div><?= htmlspecialchars($error) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div><?= htmlspecialchars($message) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-lg-6">
                <form method="GET">
                    <div class="input-group shadow-sm">
                        <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search) ?>"
                            placeholder="Search modules...">
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($modules)): ?>
        <div class="card">
            <div class="card-body empty-state">
                <i class="bi bi-book"></i>
                <h4 class="text-muted mt-3">No modules found</h4>
                <p class="text-muted">Start by adding a new module to the system.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                    Add First Module
                </button>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">
                                    Module Details
                                </th>
                                <th class="py-3 border-0">
                                    Statistics
                                </th>
                                <th class="py-3 border-0">
                                    Created
                                </th>
                                <th class="text-center py-3 border-0">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modules as $module): ?>
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <div>
                                            <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($module['module_name']) ?></h6>
                                            <span class="badge bg-primary"><?= htmlspecialchars($module['module_code']) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex gap-3">
                                            <small class="text-muted">
                                                <?= $module['question_count'] ?> Questions
                                            </small>
                                            <small class="text-muted">
                                                <?= $module['author_count'] ?> Authors
                                            </small>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <small class="text-muted">
                                            <?= date('M j, Y', strtotime($module['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td class="text-center py-3">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="?edit=<?= $module['module_id'] ?>" 
                                               class="btn btn-primary btn-sm" title="Edit Module">
                                                Edit
                                            </a>
                                            <a href="index.php?module=<?= htmlspecialchars($module['module_code']) ?>&sort=newest" 
                                               class="btn btn-info btn-sm" title="View Questions">
                                                View
                                            </a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars($module['module_code'], ENT_QUOTES) ?>? This action cannot be undone.');">
                                                <input type="hidden" name="action" value="delete_module">
                                                <input type="hidden" name="module_id" value="<?= $module['module_id'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Module">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="addModuleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Module</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_module">

                        <div class="mb-3">
                            <label for="add_module_code" class="form-label">Module Code *</label>
                            <input type="text" class="form-control" id="add_module_code" name="module_code"
                                placeholder="e.g., COMP1841" required>
                            <div class="form-text">Unique identifier for the module</div>
                        </div>

                        <div class="mb-3">
                            <label for="add_module_name" class="form-label">Module Name *</label>
                            <input type="text" class="form-control" id="add_module_name" name="module_name"
                                placeholder="e.g., Web Development" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Module</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($editModule): ?>
    <div class="modal fade show" id="editModuleModal" tabindex="-1" style="display: block;">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Edit Module: <?= htmlspecialchars($editModule['module_code']) ?>
                        </h5>
                        <a href="manage-modules.php" class="btn-close"></a>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_module">
                        <input type="hidden" name="module_id" value="<?= $editModule['module_id'] ?>">

                        <div class="mb-3">
                            <label for="edit_module_code" class="form-label">Module Code *</label>
                            <input type="text" class="form-control" id="edit_module_code" name="module_code"
                                value="<?= htmlspecialchars($editModule['module_code']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_module_name" class="form-label">Module Name *</label>
                            <input type="text" class="form-control" id="edit_module_name" name="module_name"
                                value="<?= htmlspecialchars($editModule['module_name']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="manage-modules.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Module</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>