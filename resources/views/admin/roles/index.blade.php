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
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Create New Role
            </a>
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

        <!-- Roles Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-shield-alt me-2"></i>Admin Roles</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Role Name</th>
                                <th>Type</th>
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
                                    @if($role->name === 'super_admin')
                                        <span class="badge bg-danger"><i class="fas fa-crown me-1"></i>Super Admin</span>
                                    @elseif($role->name === 'admin')
                                        <span class="badge bg-primary"><i class="fas fa-user-shield me-1"></i>Admin</span>
                                    @elseif($role->name === 'moderator')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-user-tag me-1"></i>Moderator</span>
                                    @else
                                        <span class="badge bg-info"><i class="fas fa-user-cog me-1"></i>{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($role->name, ['super_admin', 'admin', 'moderator']))
                                        <span class="badge bg-light text-dark"><i class="fas fa-lock me-1"></i>System</span>
                                    @else
                                        <span class="badge bg-light text-dark"><i class="fas fa-pencil-alt me-1"></i>Custom</span>
                                    @endif
                                </td>
                                <td>
                                    @if($role->name === 'super_admin')
                                        <span class="text-success fw-bold"><i class="fas fa-infinity me-1"></i>All ({{ $totalPermissions }})</span>
                                    @else
                                        <span class="fw-bold">{{ $role->permissions_count }}</span>
                                        <small class="text-muted">/ {{ $totalPermissions }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php $adminCount = \App\Models\Admin::where('role', $role->name)->count(); @endphp
                                    <span class="badge bg-light text-dark">{{ $adminCount }} admin(s)</span>
                                </td>
                                <td>
                                    @if($role->name === 'super_admin')
                                        <span class="text-muted"><i class="fas fa-lock me-1"></i>Non-editable</span>
                                    @else
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!in_array($role->name, ['super_admin']) && \App\Models\Admin::where('role', $role->name)->count() === 0)
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete the {{ str_replace('_', ' ', $role->name) }} role?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Admin Account Management Section -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-users-cog me-2"></i>Admin Accounts</h5>
                <a href="{{ route('admin.roles.admins.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-user-plus me-1"></i>Add Admin
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Create admin accounts and assign roles. Each admin will only see modules they have permission for.
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Admin</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($admins as $admin)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                             style="width: 36px; height: 36px; font-size: 14px;">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $admin->name }}</strong>
                                            @if($admin->phone)
                                                <br><small class="text-muted">{{ $admin->phone }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $admin->email }}</td>
                                <td>
                                    <form action="{{ route('admin.roles.assign-role', $admin) }}" method="POST">
                                        @csrf
                                        <select name="role" class="form-select form-select-sm" style="width: 170px;"
                                                onchange="this.form.submit()"
                                                {{ ($admin->role === 'super_admin' && auth('admin')->user()->role !== 'super_admin') ? 'disabled' : '' }}>
                                            @foreach($allRoles as $role)
                                                <option value="{{ $role->name }}" {{ $admin->role === $role->name ? 'selected' : '' }}>
                                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    @if($admin->id !== auth('admin')->id())
                                        <form action="{{ route('admin.roles.admins.toggle-status', $admin) }}" method="POST" class="d-inline">
                                            @csrf
                                            <span class="badge {{ $admin->status ? 'bg-success' : 'bg-secondary' }}" style="cursor:pointer; font-size: 0.8rem;" onclick="this.closest('form').submit()">
                                                {{ $admin->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </form>
                                    @else
                                        <span class="badge bg-success" style="font-size: 0.8rem;">Active (You)</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.roles.admins.edit', $admin) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($admin->id !== auth('admin')->id())
                                        <form action="{{ route('admin.roles.admins.destroy', $admin) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete admin account {{ $admin->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
                if (alert.classList.contains('show')) new bootstrap.Alert(alert).close();
            });
        }, 5000);
    </script>
</body>
</html>
