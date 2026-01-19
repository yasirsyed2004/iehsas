{{-- File: resources/views/admin/profile/edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    {{-- Include Shared Admin Styles --}}
    @include('admin.partials.styles')
    
    {{-- Page-specific styles --}}
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(50px, -50px);
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.3);
            object-fit: cover;
        }
        .profile-avatar-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .section-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
        }
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>
    {{-- Include Shared Sidebar --}}
    @include('admin.partials.sidebar', ['activeMenu' => 'profile'])

    <!-- Main Content -->
    <div class="main-content">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    @if($admin->avatar)
                        <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Avatar" class="profile-avatar">
                    @else
                        <div class="profile-avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>
                <div class="col">
                    <h2 class="mb-1">{{ $admin->name }}</h2>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-envelope me-2"></i>{{ $admin->email }}
                    </p>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-user-shield me-2"></i>{{ ucfirst(str_replace('_', ' ', $admin->role ?? 'Administrator')) }}
                    </p>
                </div>
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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Profile Information -->
                <div class="col-lg-8">
                    <div class="form-card">
                        <h4 class="section-title">
                            <i class="fas fa-user-edit me-2"></i>Profile Information
                        </h4>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $admin->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $admin->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="avatar" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                       id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Max 2MB. Supported formats: JPEG, PNG, JPG</div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="form-card">
                        <h4 class="section-title">
                            <i class="fas fa-lock me-2"></i>Change Password
                        </h4>
                        <p class="text-muted mb-4">Leave blank if you don't want to change the password.</p>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-update">
                            <i class="fas fa-save me-1"></i>Update Profile
                        </button>
                    </div>
                </div>

                <!-- Account Info Sidebar -->
                <div class="col-lg-4">
                    <div class="form-card">
                        <h4 class="section-title">
                            <i class="fas fa-info-circle me-2"></i>Account Information
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Role</label>
                            <p class="fw-bold mb-0">
                                <i class="fas fa-user-shield me-2 text-primary"></i>
                                {{ ucfirst(str_replace('_', ' ', $admin->role ?? 'Administrator')) }}
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Account Created</label>
                            <p class="fw-bold mb-0">
                                <i class="fas fa-calendar-plus me-2 text-success"></i>
                                {{ $admin->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small">Last Updated</label>
                            <p class="fw-bold mb-0">
                                <i class="fas fa-calendar-check me-2 text-info"></i>
                                {{ $admin->updated_at->format('M d, Y H:i') }}
                            </p>
                        </div>

                        @if($admin->last_login_at)
                        <div class="mb-0">
                            <label class="form-label text-muted small">Last Login</label>
                            <p class="fw-bold mb-0">
                                <i class="fas fa-sign-in-alt me-2 text-warning"></i>
                                {{ \Carbon\Carbon::parse($admin->last_login_at)->format('M d, Y H:i') }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <div class="form-card bg-light">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-lightbulb me-2"></i>Tips
                        </h6>
                        <ul class="small text-muted mb-0">
                            <li class="mb-2">Use a strong password with at least 8 characters</li>
                            <li class="mb-2">Include uppercase, lowercase, numbers, and symbols</li>
                            <li>Keep your email updated for account recovery</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>