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
    - registered-students
    - courses
    - course-categories
    - enrollments
    - departments
    - tasks
    - dar
    - notifications
    - roles
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
        @can('view_dashboard')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'dashboard' ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        @endcan

        {{-- Users Management --}}
        @can('view_users')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'users' ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="fas fa-users me-2"></i> Users Management
            </a>
        </li>
        @endcan

        {{-- Entry Tests --}}
        @can('view_entry_tests')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'entry-tests' ? 'active' : '' }}"
               href="{{ route('admin.entry-tests.index') }}">
                <i class="fas fa-clipboard-list me-2"></i> Entry Tests
            </a>
        </li>
        @endcan

        {{-- Question Bank --}}
        @can('view_questions')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'questions' ? 'active' : '' }}"
               href="{{ route('admin.questions.index') }}">
                <i class="fas fa-question-circle me-2"></i> Question Bank
            </a>
        </li>
        @endcan

        {{-- Student Attempts --}}
        @can('view_student_attempts')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'student-attempts' ? 'active' : '' }}"
               href="{{ route('admin.student-attempts.index') }}">
                <i class="fas fa-chart-line me-2"></i> Student Attempts
            </a>
        </li>
        @endcan

        {{-- Registered Students --}}
        @can('view_registered_students')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'registered-students' ? 'active' : '' }}"
               href="{{ route('admin.registered-students.index') }}"
               style="white-space: nowrap;">
                <i class="fas fa-id-card me-2"></i> Reg. Students
            </a>
        </li>
        @endcan

        {{-- Courses --}}
        @can('view_courses')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'courses' ? 'active' : '' }}"
               href="{{ route('admin.courses.index') }}">
                <i class="fas fa-graduation-cap me-2"></i> Courses
            </a>
        </li>
        @endcan

        {{-- Course Categories --}}
        @can('view_course_categories')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'course-categories' ? 'active' : '' }}"
               href="{{ route('admin.course-categories.index') }}">
                <i class="fas fa-tags me-2"></i> Course Categories
            </a>
        </li>
        @endcan

        {{-- Enrollments --}}
        @can('view_enrollments')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'enrollments' ? 'active' : '' }}"
               href="{{ route('admin.enrollments.index') }}">
                <i class="fas fa-user-graduate me-2"></i> Enrollments
            </a>
        </li>
        @endcan

        {{-- Task Management Section --}}
        @canany(['view_departments', 'view_tasks', 'view_dar'])
        <li class="nav-item mt-3">
            <hr class="border-secondary opacity-25 my-2">
        </li>
        <li class="nav-item">
            <small class="text-uppercase px-3 opacity-50" style="font-size: 0.65rem; letter-spacing: 1px; color: rgba(255,255,255,0.6);">Task Management</small>
        </li>
        @endcanany

        {{-- Departments --}}
        @can('view_departments')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'departments' ? 'active' : '' }}"
               href="{{ route('admin.departments.index') }}">
                <i class="fas fa-building me-2"></i> Departments
            </a>
        </li>
        @endcan

        {{-- Tasks --}}
        @can('view_tasks')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'tasks' ? 'active' : '' }}"
               href="{{ route('admin.tasks.index') }}">
                <i class="fas fa-tasks me-2"></i> Tasks
            </a>
        </li>
        @endcan

        {{-- Daily Reports (DAR) --}}
        @can('view_dar')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'dar' ? 'active' : '' }}"
               href="{{ route('admin.dar.index') }}">
                <i class="fas fa-file-alt me-2"></i> Daily Reports
            </a>
        </li>
        @endcan

        {{-- Notifications --}}
        @can('view_notifications')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'notifications' ? 'active' : '' }}"
               href="{{ route('admin.notifications.index') }}">
                <i class="fas fa-bell me-2"></i> Notifications
                @php
                    $unreadCount = \App\Models\Notification::forAdmin(Auth::guard('admin')->id())->unread()->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="badge bg-danger ms-1">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </a>
        </li>
        @endcan

        {{-- Roles & Permissions --}}
        @can('manage_roles_permissions')
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'roles' ? 'active' : '' }}"
               href="{{ route('admin.roles.index') }}">
                <i class="fas fa-shield-alt me-2"></i> Roles & Permissions
            </a>
        </li>
        @endcan

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
