<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User - Admin LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1000;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: #ecf0f1;
            border-radius: 5px;
            margin: 2px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: #3498db;
            color: white;
        }
        .user-info {
            color: #ecf0f1;
            padding: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
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
    <nav class="sidebar">
        <div class="user-info text-center">
            <div class="mb-2">
                <i class="fas fa-user-circle fa-3x"></i>
            </div>
            <h6>{{ Auth::guard('admin')->user()->name }}</h6>
            <small>{{ ucfirst(str_replace('_', ' ', Auth::guard('admin')->user()->role)) }}</small>
        </div>
        
        <ul class="nav flex-column p-3">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-2"></i> Users Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="alert('Coming Soon!')">
                    <i class="fas fa-clipboard-list me-2"></i> Entry Tests
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="alert('Coming Soon!')">
                    <i class="fas fa-question-circle me-2"></i> Question Bank
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="alert('Coming Soon!')">
                    <i class="fas fa-book me-2"></i> E-Learning
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="alert('Coming Soon!')">
                    <i class="fas fa-chart-bar me-2"></i> Reports
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link" href="{{ route('admin.profile') }}">
                    <i class="fas fa-user-cog me-2"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </nav>

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
                        
                        @if($user->role == 'admin')
                            <span class="badge bg-danger fs-6">Admin</span>
                        @elseif($user->role == 'teacher')
                            <span class="badge bg-info fs-6">Teacher</span>
                        @else
                            <span class="badge bg-primary fs-6">Student</span>
                        @endif
                        
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>