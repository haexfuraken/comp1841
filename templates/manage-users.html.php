<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Student Q&A System</title>
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
    
    .user-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
    }

    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .management-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }
    
    .role-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .badge-student {
        background-color: #0d6efd;
    }

    .badge-staff {
        background-color: #198754;
    }
    
    .btn-solid-dark {
        background-color: #212529 !important;
        border-color: #212529 !important;
        color: #ffffff !important;
    }

    .btn-solid-dark:hover {
        background-color: #1c1f23 !important;
        border-color: #1c1f23 !important;
        color: #ffffff !important;
    }

    .btn-solid-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .btn-solid-danger:hover {
        background-color: #c82333 !important;
        border-color: #c82333 !important;
        color: #ffffff !important;
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

    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background-color: #f8f9fa;
        border: none;
        font-weight: 600;
        color: #495057;
    }

    .table td {
        border-color: #e9ecef;
        vertical-align: middle;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .alert {
        border: none;
        border-radius: 8px;
    }

    .pagination .page-link {
        border: none;
        color: #667eea;
    }

    .pagination .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1rem;
    }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <div>
                <h1 class="h2 fw-bold mb-1">
                    Manage Users
                </h1>
                <p class="text-muted mb-0">Create and manage student and staff accounts</p>
            </div>
            <div class="text-end">
                <span class="badge bg-primary fs-6">
                    <?= $totalUsers ?> users
                </span>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <div><?= htmlspecialchars($message) ?></div>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light border-0">
                <h6 class="card-title mb-0 text-muted fw-semibold">
                    Search & Filter Users
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Search Users
                        </label>
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by username, email, or name..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Filter by Role
                        </label>
                        <select class="form-select" name="role">
                            <option value="">All Roles</option>
                            <option value="student" <?= $roleFilter === 'student' ? 'selected' : '' ?>>Students Only</option>
                            <option value="staff" <?= $roleFilter === 'staff' ? 'selected' : '' ?>>Staff Only</option>
                            <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin Only</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 text-muted fw-semibold">
                        User List
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">
                            <?= count($users) ?> shown
                        </span>
                        <span class="badge bg-secondary">
                            <?= $totalUsers ?> total
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($users)): ?>
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <div style="font-size: 4rem; color: #6c757d;">👥</div>
                        </div>
                        <h5 class="text-muted">No Users Found</h5>
                        <p class="text-muted mb-4">Try adjusting your search criteria or create a new user account.</p>
                        <button onclick="document.getElementById('username').focus()" class="btn btn-outline-primary">
                            Add First User
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">
                                        User Details
                                    </th>
                                    <th class="py-3 border-0">
                                        Contact
                                    </th>
                                    <th class="py-3 border-0">
                                        Role
                                    </th>
                                    <th class="py-3 border-0">
                                        Joined
                                    </th>
                                    <th class="text-center py-3 border-0">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr class="border-bottom">
                                        <td class="px-4 py-3">
                                            <div>
                                                <h6 class="mb-0 fw-semibold"><?= htmlspecialchars($user['username']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($user['full_name']) ?></small>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <?= htmlspecialchars($user['email']) ?>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge <?= 
                                                ($user['role'] === 'student') ? 'bg-info' : 
                                                (($user['role'] === 'staff') ? 'bg-success' : 'bg-danger') 
                                            ?> px-2 py-1">
                                                <?= ucfirst($user['role']) ?>
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <small class="text-muted d-flex align-items-center">
                                                <?= date('M j, Y', strtotime($user['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td class="text-center py-3">
                                            <div class="d-flex justify-content-center gap-2">
                                                <?php if ($auth->canEditUser($user['user_id'], $currentUser['user_id'])): ?>
                                                    <button type="button" class="btn btn-primary btn-sm" 
                                                            onclick="editUser(<?= $user['user_id'] ?>, '<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>', '<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>', '<?= htmlspecialchars($user['full_name'], ENT_QUOTES) ?>', '<?= $user['role'] ?>')" 
                                                            title="Edit User">
                                                        Edit
                                                    </button>
                                                    <a href="user-activity.php?user_id=<?= $user['user_id'] ?>" class="btn btn-info btn-sm" title="View User Activity">
                                                        Activity
                                                    </a>
                                                    <form method="POST" style="display: inline;" 
                                                          onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                                        <input type="hidden" name="action" value="delete_user">
                                                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete User">
                                                            Delete
                                                        </button>
                                                    </form>
                                                <?php elseif ($user['user_id'] === $currentUser['user_id']): ?>
                                                    <button class="btn btn-secondary btn-sm" disabled title="Cannot edit own account">
                                                        Own Account
                                                    </button>
                                                <?php else: ?>
                                                    <a href="user-activity.php?user_id=<?= $user['user_id'] ?>" class="btn btn-info btn-sm" title="View User Activity">
                                                        Activity
                                                    </a>
                                                    <button class="btn btn-secondary btn-sm" disabled title="Insufficient permissions">
                                                        Restricted
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Users pagination">
                    <ul class="pagination pagination-lg">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $roleFilter ? '&role=' . urlencode($roleFilter) : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <button type="button" class="btn btn-solid-dark px-4" data-bs-toggle="modal" data-bs-target="#addUserModal">
                Add New User
            </button>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" id="addUserForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_user">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_username" class="form-label fw-semibold">Username</label>
                                <input type="text" class="form-control" id="modal_username" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_email" class="form-label fw-semibold">Email Address</label>
                                <input type="email" class="form-control" id="modal_email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_full_name" class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control" id="modal_full_name" name="full_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_role" class="form-label fw-semibold">User Role</label>
                                <select class="form-select" id="modal_role" name="role" required>
                                    <option value="">Select Role</option>
                                    <?php if ($auth->isAdmin()): ?>
                                        <option value="student">Student</option>
                                        <option value="staff">Staff</option>
                                    <?php elseif ($auth->isStaff()): ?>
                                        <option value="student">Student</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_password" class="form-label fw-semibold">Password</label>
                                <input type="password" class="form-control" id="modal_password" name="password" required>
                                <small class="text-muted">Minimum 6 characters required</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-solid-dark">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_user">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_username" class="form-label fw-semibold">Username</label>
                                <input type="text" class="form-control" id="edit_username" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_email" class="form-label fw-semibold">Email Address</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_full_name" class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_role" class="form-label fw-semibold">User Role</label>
                                <select class="form-select" id="edit_role" name="role" required>
                                    <option value="">Select Role</option>
                                    <?php if ($auth->isAdmin()): ?>
                                        <option value="student">Student</option>
                                        <option value="staff">Staff</option>
                                    <?php elseif ($auth->isStaff()): ?>
                                        <option value="student">Student</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-solid-dark">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        function editUser(userId, username, email, fullName, role) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_full_name').value = fullName;
            document.getElementById('edit_role').value = role;
            
            
            if (role === 'admin') {
                const roleSelect = document.getElementById('edit_role');
                
                if (!roleSelect.querySelector('option[value="admin"]')) {
                    const adminOption = document.createElement('option');
                    adminOption.value = 'admin';
                    adminOption.textContent = 'Admin (Cannot change admin role)';
                    adminOption.disabled = true;
                    roleSelect.appendChild(adminOption);
                }
                roleSelect.value = 'admin';
                roleSelect.disabled = true;
                
                
                const hiddenRoleInput = document.createElement('input');
                hiddenRoleInput.type = 'hidden';
                hiddenRoleInput.name = 'role';
                hiddenRoleInput.value = 'admin';
                document.getElementById('editUserForm').appendChild(hiddenRoleInput);
            } else {
                document.getElementById('edit_role').disabled = false;
                
                const adminOption = document.querySelector('#edit_role option[value="admin"]');
                if (adminOption) adminOption.remove();
                const hiddenInput = document.querySelector('#editUserForm input[name="role"][type="hidden"]');
                if (hiddenInput) hiddenInput.remove();
            }
            
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        
        document.getElementById('addUserModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('addUserForm').reset();
        });

        
        document.getElementById('editUserModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('editUserForm').reset();
            document.getElementById('edit_role').disabled = false;
        });
    </script>
</body>
</html>