<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Role - Admin LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('admin.partials.styles')

    <style>
        .permission-module {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        .permission-module-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 12px 18px;
            font-weight: 600;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .permission-module-body { padding: 15px 18px; }
        .permission-checkbox {
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.2s;
            margin-bottom: 5px;
        }
        .permission-checkbox:hover { background: rgba(102, 126, 234, 0.05); }
        .permission-checkbox .form-check-input:checked { background-color: #667eea; border-color: #667eea; }
        .permission-name { font-size: 0.9rem; color: #495057; }
        .module-select-all { cursor: pointer; font-size: 0.8rem; color: #667eea; }
        .module-select-all:hover { text-decoration: underline; }
    </style>
</head>
<body>
    @include('admin.partials.sidebar', ['activeMenu' => 'roles'])

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-plus-circle me-2"></i>Create New Role</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
                        <li class="breadcrumb-item active">Create Role</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Roles
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <!-- Role Name -->
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-tag me-2"></i>Role Information</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="e.g., content_manager"
                                   id="roleNameInput"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Lowercase letters, numbers, and underscores only. Example: content_manager</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div>
                                <label class="form-label fw-bold">Display Preview</label>
                                <div class="fs-5">
                                    <span class="badge bg-secondary" id="rolePreview">New Role</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="fw-bold text-muted me-2">Quick Actions:</span>
                    <button type="button" class="btn btn-sm btn-outline-success" id="selectAll">
                        <i class="fas fa-check-double me-1"></i>Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="deselectAll">
                        <i class="fas fa-times me-1"></i>Deselect All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info" id="selectAllView">
                        <i class="fas fa-eye me-1"></i>Select All View
                    </button>
                    <div class="ms-auto">
                        <span id="permissionCount" class="badge bg-primary fs-6">0 / {{ $allPermissions->count() }}</span>
                        <span class="text-muted ms-1">permissions selected</span>
                    </div>
                </div>
            </div>

            <!-- Permission Modules -->
            <div class="row">
                @foreach($modules as $moduleName => $modulePermissions)
                <div class="col-md-6">
                    <div class="permission-module">
                        <div class="permission-module-header">
                            <div><i class="fas fa-cube me-2 text-primary"></i>{{ $moduleName }}</div>
                            <span class="module-select-all" data-module="{{ Str::slug($moduleName) }}">Toggle All</span>
                        </div>
                        <div class="permission-module-body">
                            @foreach($modulePermissions as $permName)
                                @if($allPermissions->has($permName))
                                <div class="permission-checkbox">
                                    <div class="form-check">
                                        <input class="form-check-input permission-input" type="checkbox"
                                               name="permissions[]" value="{{ $permName }}"
                                               id="perm_{{ $permName }}" data-module="{{ Str::slug($moduleName) }}"
                                               {{ in_array($permName, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label permission-name" for="perm_{{ $permName }}">
                                            {{ ucwords(str_replace('_', ' ', $permName)) }}
                                        </label>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Submit -->
            <div class="card mt-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <span class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        The new role will be available for assignment to admin users immediately after creation.
                    </span>
                    <div>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Create Role
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateCount() {
            const total = document.querySelectorAll('.permission-input').length;
            const checked = document.querySelectorAll('.permission-input:checked').length;
            document.getElementById('permissionCount').textContent = checked + ' / ' + total;
        }

        document.getElementById('selectAll').addEventListener('click', function() {
            document.querySelectorAll('.permission-input').forEach(cb => cb.checked = true);
            updateCount();
        });
        document.getElementById('deselectAll').addEventListener('click', function() {
            document.querySelectorAll('.permission-input').forEach(cb => cb.checked = false);
            updateCount();
        });
        document.getElementById('selectAllView').addEventListener('click', function() {
            document.querySelectorAll('.permission-input').forEach(cb => {
                if (cb.value.startsWith('view_')) cb.checked = true;
            });
            updateCount();
        });
        document.querySelectorAll('.module-select-all').forEach(btn => {
            btn.addEventListener('click', function() {
                const module = this.dataset.module;
                const checkboxes = document.querySelectorAll('.permission-input[data-module="' + module + '"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
                updateCount();
            });
        });
        document.querySelectorAll('.permission-input').forEach(cb => cb.addEventListener('change', updateCount));

        // Role name preview
        document.getElementById('roleNameInput').addEventListener('input', function() {
            const val = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
            this.value = val;
            document.getElementById('rolePreview').textContent = val ? val.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : 'New Role';
        });

        updateCount();
    </script>
</body>
</html>
