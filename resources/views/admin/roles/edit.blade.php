<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role Permissions - Admin LMS</title>
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
        .permission-module-body {
            padding: 15px 18px;
        }
        .permission-checkbox {
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.2s;
            margin-bottom: 5px;
        }
        .permission-checkbox:hover {
            background: rgba(102, 126, 234, 0.05);
        }
        .permission-checkbox .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        .permission-name {
            font-size: 0.9rem;
            color: #495057;
        }
        .module-select-all {
            cursor: pointer;
            font-size: 0.8rem;
            color: #667eea;
        }
        .module-select-all:hover {
            text-decoration: underline;
        }
        .bulk-actions {
            position: sticky;
            top: 0;
            z-index: 10;
            background: white;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    @include('admin.partials.sidebar', ['activeMenu' => 'roles'])

    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Edit Permissions: {{ ucfirst(str_replace('_', ' ', $role->name)) }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles & Permissions</a></li>
                        <li class="breadcrumb-item active">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</li>
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

        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Bulk Actions -->
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
                        <span id="permissionCount" class="badge bg-primary fs-6">
                            {{ count($rolePermissions) }} / {{ $allPermissions->count() }}
                        </span>
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
                            <div>
                                <i class="fas fa-cube me-2 text-primary"></i>{{ $moduleName }}
                            </div>
                            <span class="module-select-all" data-module="{{ Str::slug($moduleName) }}">
                                Toggle All
                            </span>
                        </div>
                        <div class="permission-module-body">
                            @foreach($modulePermissions as $permName)
                                @if($allPermissions->has($permName))
                                <div class="permission-checkbox">
                                    <div class="form-check">
                                        <input class="form-check-input permission-input"
                                               type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permName }}"
                                               id="perm_{{ $permName }}"
                                               data-module="{{ Str::slug($moduleName) }}"
                                               {{ in_array($permName, $rolePermissions) ? 'checked' : '' }}>
                                        <label class="form-check-label permission-name" for="perm_{{ $permName }}">
                                            @php
                                                $label = str_replace('_', ' ', $permName);
                                                $label = ucwords($label);
                                            @endphp
                                            {{ $label }}
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
                        Changes will take effect immediately for all admins with the <strong>{{ str_replace('_', ' ', $role->name) }}</strong> role.
                    </span>
                    <div>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Permissions
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

        // Bulk select all
        document.getElementById('selectAll').addEventListener('click', function() {
            document.querySelectorAll('.permission-input').forEach(cb => cb.checked = true);
            updateCount();
        });

        // Bulk deselect all
        document.getElementById('deselectAll').addEventListener('click', function() {
            document.querySelectorAll('.permission-input').forEach(cb => cb.checked = false);
            updateCount();
        });

        // Select all view permissions
        document.getElementById('selectAllView').addEventListener('click', function() {
            document.querySelectorAll('.permission-input').forEach(cb => {
                if (cb.value.startsWith('view_')) {
                    cb.checked = true;
                }
            });
            updateCount();
        });

        // Module-level toggle
        document.querySelectorAll('.module-select-all').forEach(btn => {
            btn.addEventListener('click', function() {
                const module = this.dataset.module;
                const checkboxes = document.querySelectorAll('.permission-input[data-module="' + module + '"]');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
                updateCount();
            });
        });

        // Update count on individual change
        document.querySelectorAll('.permission-input').forEach(cb => {
            cb.addEventListener('change', updateCount);
        });
    </script>
</body>
</html>
