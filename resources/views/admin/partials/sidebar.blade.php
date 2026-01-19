{{-- File: resources/views/admin/partials/sidebar.blade.php --}}
{{-- 
    Shared Admin Sidebar Component
    Usage: @include('admin.partials.sidebar', ['activeMenu' => 'dashboard'])
    
    Available activeMenu values:
    - dashboard
    - users
    - entry-tests
    - questions
    - student-attempts
    - courses
    - course-categories
    - enrollments
    - e-learning
    - reports
    - profile
--}}

<nav class="sidebar">
    <!-- User Info Section -->
    <div class="user-info text-center">
        <div class="mb-2">
            @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->avatar)
                <img src="{{ asset('storage/' . Auth::guard('admin')->user()->avatar) }}" 
                     alt="Avatar" 
                     class="rounded-circle" 
                     width="60" 
                     height="60"
                     style="object-fit: cover; border: 3px solid rgba(255,255,255,0.2);">
            @else
                <i class="fas fa-user-circle fa-3x"></i>
            @endif
        </div>
        <h6 class="mb-1">{{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : 'Admin' }}</h6>
        <small class="opacity-75">{{ Auth::guard('admin')->check() ? ucfirst(str_replace('_', ' ', Auth::guard('admin')->user()->role ?? 'admin')) : 'Administrator' }}</small>
    </div>
    
    <!-- Navigation Menu -->
    <ul class="nav flex-column p-3">
        {{-- Dashboard --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'dashboard' ? 'active' : '' }}" 
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        
        {{-- Users Management --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'users' ? 'active' : '' }}" 
               href="{{ route('admin.users.index') }}">
                <i class="fas fa-users me-2"></i> Users Management
            </a>
        </li>
        
        {{-- Entry Tests --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'entry-tests' ? 'active' : '' }}" 
               href="{{ route('admin.entry-tests.index') }}">
                <i class="fas fa-clipboard-list me-2"></i> Entry Tests
            </a>
        </li>
        
        {{-- Question Bank --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'questions' ? 'active' : '' }}" 
               href="{{ route('admin.questions.index') }}">
                <i class="fas fa-question-circle me-2"></i> Question Bank
            </a>
        </li>
        
        {{-- Student Attempts --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'student-attempts' ? 'active' : '' }}" 
               href="{{ route('admin.student-attempts.index') }}">
                <i class="fas fa-chart-line me-2"></i> Student Attempts
            </a>
        </li>
        
        {{-- Courses --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'courses' ? 'active' : '' }}" 
               href="{{ route('admin.courses.index') }}">
                <i class="fas fa-graduation-cap me-2"></i> Courses
            </a>
        </li>
        
        {{-- Course Categories --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'course-categories' ? 'active' : '' }}" 
               href="{{ route('admin.course-categories.index') }}">
                <i class="fas fa-tags me-2"></i> Course Categories
            </a>
        </li>
        
        {{-- Enrollments --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'enrollments' ? 'active' : '' }}" 
               href="{{ route('admin.enrollments.index') }}">
                <i class="fas fa-user-graduate me-2"></i> Enrollments
            </a>
        </li>
        
        {{-- E-Learning (Coming Soon) --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'e-learning' ? 'active' : '' }}" 
               href="#" onclick="alert('Coming Soon!')">
                <i class="fas fa-book me-2"></i> E-Learning
            </a>
        </li>
        
        {{-- Reports (Coming Soon) --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'reports' ? 'active' : '' }}" 
               href="#" onclick="alert('Coming Soon!')">
                <i class="fas fa-chart-bar me-2"></i> Reports
            </a>
        </li>
        
        {{-- Separator --}}
        <li class="nav-item mt-3">
            <hr class="border-secondary opacity-25 my-2">
        </li>
        
        {{-- Profile --}}
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'profile' ? 'active' : '' }}" 
               href="{{ route('admin.profile') }}">
                <i class="fas fa-user-cog me-2"></i> Profile
            </a>
        </li>
        
        {{-- Logout --}}
        <li class="nav-item">
            <a class="nav-link text-danger-light" 
               href="#" 
               onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
            <form id="sidebar-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</nav>