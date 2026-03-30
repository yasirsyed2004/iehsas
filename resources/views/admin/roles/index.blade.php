<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions - Admin LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('admin.partials.styles')
</head>
<body>
    @include('admin.partials.sidebar', ['activeMenu' => 'roles'])

    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Roles & Permissions</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Roles & Permissions</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Info Card -->
        <div class="alert alert-info mb-4">
            <i class="fas fa-info-circle me-2"></i>
            Manage what each admin role can access. <strong>Super Admin</strong> always has full access and cannot be restricted.
        </div>

        <!-- Roles Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shield-alt me-2"></i>Admin Roles
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th>Admins</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $index => $role)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($role->name === 'super_admin')
                                            <span class="badge bg-danger me-2"><i class="fas fa-crown me-1"></i>Super Admin</span>
                                        @elseif($role->name === 'admin')
                                            <span class="badge bg-primary me-2"><i class="fas fa-user-shield me-1"></i>Admin</span>
                                        @elseif($role->name === 'moderator')
                                            <span class="badge bg-warning text-dark me-2"><i class="fas fa-user-tag me-1"></i>Moderator</span>
                                        @else
                                            <span class="badge bg-secondary me-2">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($role->name === 'super_admin')
                                        <span class="text-success fw-bold">
                                            <i class="fas fa-infinity me-1"></i>All ({{ $totalPermissions }})
                                        </span>
                                    @else
                                        <span class="fw-bold">{{ $role->permissions_count }}</span>
                                        <small class="text-muted">/ {{ $totalPermissions }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $adminCount = \App\Models\Admin::where('role', $role->name)->count();
                                    @endphp
                                    <span class="badge bg-light text-dark">{{ $adminCount }} admin(s)</span>
                                </td>
                                <td>
                                    @if($role->name === 'super_admin')
                                        <span class="text-muted"><i class="fas fa-lock me-1"></i>Non-editable</span>
                                    @else
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit Permissions
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                if (alert.classList.contains('show')) {
                    new bootstrap.Alert(alert).close();
                }
            });
        }, 5000);
    </script>
</body>
</html>
