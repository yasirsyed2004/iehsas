<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - Admin LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Include Shared Admin Styles --}}
    @include('admin.partials.styles')

    {{-- Page-specific styles --}}
    <style>
        .info-label {
            font-weight: 600;
            color: #6c757d;
        }
        .info-value {
            color: #495057;
        }
        .user-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    {{-- Include Shared Sidebar --}}
    @include('admin.partials.sidebar', ['activeMenu' => 'users'])

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>User Details</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">{{ $user->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </a>
            </div>
        </div>

        <div class="row">
            <!-- User Profile Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        
                        @php
                            $roleBadges = [
                                'admin' => 'bg-danger',
                                'teacher' => 'bg-info',
                                'staff' => 'bg-warning text-dark',
                                'student' => 'bg-primary',
                            ];
                            $badgeClass = $roleBadges[$user->role] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $badgeClass }} fs-6">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
                        
                        @if($user->status)
                            <span class="badge bg-success fs-6 ms-2">Active</span>
                        @else
                            <span class="badge bg-danger fs-6 ms-2">Inactive</span>
                        @endif

                        <div class="mt-3">
                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-{{ $user->status ? 'warning' : 'success' }}">
                                    <i class="fas fa-toggle-{{ $user->status ? 'on' : 'off' }} me-1"></i>
                                    {{ $user->status ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Information -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>User Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Full Name:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->name }}</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Email:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->email }}</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Role:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ ucfirst($user->role) }}</span>
                            </div>
                        </div>

                        @if($user->phone)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Phone:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->phone }}</span>
                            </div>
                        </div>
                        @endif

                        @if($user->student_id)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Student ID:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->student_id }}</span>
                            </div>
                        </div>
                        @endif

                        @if($user->date_of_birth)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Date of Birth:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->date_of_birth->format('M d, Y') }}</span>
                            </div>
                        </div>
                        @endif

                        @if($user->gender)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Gender:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ ucfirst($user->gender) }}</span>
                            </div>
                        </div>
                        @endif

                        @if($user->address)
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Address:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->address }}</span>
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Status:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->status ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Created:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->created_at->format('M d, Y \a\t g:i A') }}</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Last Updated:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->updated_at->format('M d, Y \a\t g:i A') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($user->role == 'staff')
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-id-badge me-2"></i>Staff Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Department:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->department->name ?? 'Not Assigned' }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <span class="info-label">Reporting Manager:</span>
                            </div>
                            <div class="col-sm-9">
                                <span class="info-value">{{ $user->reportingManager->name ?? 'Not Assigned' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>