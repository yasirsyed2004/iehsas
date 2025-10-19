{{-- File: resources/views/admin/enrollments/test-email.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Configuration - Admin LMS</title>
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
        .config-status {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .config-status.configured {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .config-status.not-configured {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="user-info text-center">
            <div class="mb-2">
                @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->avatar)
                    <img src="{{ asset('storage/' . Auth::guard('admin')->user()->avatar) }}" alt="Avatar" class="rounded-circle" width="60" height="60">
                @else
                    <i class="fas fa-user-circle fa-3x"></i>
                @endif
            </div>
            <h6>{{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : 'Admin' }}</h6>
            <small>{{ Auth::guard('admin')->check() ? ucfirst(str_replace('_', ' ', Auth::guard('admin')->user()->role ?? 'admin')) : 'Administrator' }}</small>
        </div>
        
        <ul class="nav flex-column p-3">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-2"></i> Users Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.entry-tests.index') }}">
                    <i class="fas fa-clipboard-list me-2"></i> Entry Tests
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.questions.index') }}">
                    <i class="fas fa-question-circle me-2"></i> Question Bank
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.student-attempts.index') }}">
                    <i class="fas fa-chart-line me-2"></i> Student Attempts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.courses.index') }}">
                    <i class="fas fa-graduation-cap me-2"></i> Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.course-categories.index') }}">
                    <i class="fas fa-tags me-2"></i> Course Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.enrollments.index') }}">
                    <i class="fas fa-user-graduate me-2"></i> Enrollments
                </a>
            </li>
        </ul>
        
        <!-- Logout -->
        <div class="p-3 mt-auto">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">Test Email Configuration</h1>
                            <p class="text-muted">Test email functionality for enrollment notifications</p>
                        </div>
                        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Enrollments
                        </a>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <!-- Email Configuration Status -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cog me-2"></i> Current Configuration
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(isset($config))
                                <div class="config-status {{ $config['is_configured'] ? 'configured' : 'not-configured' }}">
                                    <div class="d-flex align-items-center">
                                        <i class="fas {{ $config['is_configured'] ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-2"></i>
                                        <strong>{{ $config['is_configured'] ? 'Configured' : 'Not Configured' }}</strong>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <strong>Mail Driver:</strong>
                                    <span class="badge bg-secondary">{{ $config['mailer'] ?? 'Not set' }}</span>
                                </div>
                                
                                @if(isset($config['host']))
                                    <div class="mb-3">
                                        <strong>SMTP Host:</strong>
                                        <br>
                                        <small class="text-muted">{{ $config['host'] }}</small>
                                    </div>
                                @endif
                                
                                @if(isset($config['port']))
                                    <div class="mb-3">
                                        <strong>SMTP Port:</strong>
                                        <span class="badge bg-info">{{ $config['port'] }}</span>
                                    </div>
                                @endif

                                @if(isset($config['from_address']))
                                    <div class="mb-3">
                                        <strong>From Address:</strong>
                                        <br>
                                        <small class="text-muted">{{ $config['from_address'] }}</small>
                                    </div>
                                @endif

                                @if(isset($config['from_name']))
                                    <div class="mb-3">
                                        <strong>From Name:</strong>
                                        <br>
                                        <small class="text-muted">{{ $config['from_name'] }}</small>
                                    </div>
                                @endif
                            @endif

                            <hr>
                            
                            <div class="alert alert-info mb-0">
                                <small>
                                    <i class="fas fa-lightbulb me-1"></i>
                                    <strong>Current Mail Driver:</strong> {{ config('mail.default') }}
                                    <br>
                                    @if(config('mail.default') === 'log')
                                        <span class="text-warning">⚠️ Using 'log' driver - emails will be logged, not sent</span>
                                    @elseif(config('mail.default') === 'smtp')
                                        <span class="text-success">✅ Using SMTP - emails will be sent</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Email Form -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-envelope me-2"></i> Send Test Email
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.enrollments.test-email.send') }}">
                                @csrf
                                
                                {{-- FIXED: Changed name from 'email' to 'test_email' to match controller validation --}}
                                <div class="mb-3">
                                    <label for="test_email" class="form-label">Test Email Address</label>
                                    <input type="email" 
                                           class="form-control @error('test_email') is-invalid @enderror" 
                                           id="test_email" 
                                           name="test_email" 
                                           value="{{ old('test_email', Auth::guard('admin')->user()->email ?? '') }}" 
                                           required>
                                    @error('test_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Enter the email address where you want to send the test email.
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i> Send Test Email
                                    </button>
                                </div>
                            </form>

                            <hr>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6>📧 Email Settings Guide:</h6>
                                    <small>
                                        <strong>For Gmail:</strong><br>
                                        1. Enable 2-Factor Authentication<br>
                                        2. Generate App Password<br>
                                        3. Use App Password in .env file<br>
                                        4. Set MAIL_MAILER=smtp
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <h6>🔧 Current .env Settings:</h6>
                                    <small>
                                        <strong>MAIL_MAILER:</strong> {{ config('mail.default') }}<br>
                                        <strong>MAIL_HOST:</strong> {{ config('mail.mailers.smtp.host') ?? 'Not set' }}<br>
                                        <strong>MAIL_PORT:</strong> {{ config('mail.mailers.smtp.port') ?? 'Not set' }}<br>
                                        <strong>MAIL_USERNAME:</strong> {{ config('mail.mailers.smtp.username') ? 'Set' : 'Not set' }}<br>
                                        <strong>MAIL_PASSWORD:</strong> {{ config('mail.mailers.smtp.password') ? 'Set' : 'Not set' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Log (if using log driver) -->
                    @if(config('mail.default') === 'log')
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-file-alt me-2"></i> Email Logs
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <strong>Log Driver Active:</strong> Emails are being logged to <code>storage/logs/laravel.log</code> instead of being sent.
                                    <br>
                                    <small>To send real emails, change <code>MAIL_MAILER=log</code> to <code>MAIL_MAILER=smtp</code> in your .env file.</small>
                                </div>
                                
                                <p>Check your email logs at:</p>
                                <code>storage/logs/laravel.log</code>
                                
                                <div class="mt-3">
                                    <a href="#" onclick="alert('You can check the Laravel logs in your project\'s storage/logs/laravel.log file to see the email content.');" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-external-link-alt me-1"></i> View Log Instructions
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>