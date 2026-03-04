{{-- File: resources/views/staff/partials/sidebar.blade.php --}}
<nav class="sidebar">
    <div class="user-info text-center">
        <div class="mb-2">
            <i class="fas fa-user-circle fa-3x"></i>
        </div>
        <h6 class="mb-1">{{ Auth::guard('web')->user()->name ?? 'Staff' }}</h6>
        <small class="opacity-75">Staff Member</small>
        @if(Auth::guard('web')->user()->department)
            <br><small class="opacity-50">{{ Auth::guard('web')->user()->department->name }}</small>
        @endif
    </div>

    <ul class="nav flex-column p-3">
        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'dashboard' ? 'active' : '' }}"
               href="{{ route('staff.dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'tasks' ? 'active' : '' }}"
               href="{{ route('staff.tasks.index') }}">
                <i class="fas fa-tasks me-2"></i> My Tasks
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'dar' ? 'active' : '' }}"
               href="{{ route('staff.dar.index') }}">
                <i class="fas fa-file-alt me-2"></i> Daily Reports
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'notifications' ? 'active' : '' }}"
               href="{{ route('staff.notifications.index') }}">
                <i class="fas fa-bell me-2"></i> Notifications
                @php
                    $staffUnread = \App\Models\Notification::where('notifiable_type', 'user')
                        ->where('notifiable_id', Auth::guard('web')->id())
                        ->where('is_read', false)->count();
                @endphp
                @if($staffUnread > 0)
                    <span class="badge bg-danger ms-1">{{ $staffUnread > 99 ? '99+' : $staffUnread }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item mt-3">
            <hr class="border-secondary opacity-25 my-2">
        </li>

        <li class="nav-item">
            <a class="nav-link {{ ($activeMenu ?? '') === 'profile' ? 'active' : '' }}"
               href="{{ route('staff.profile') }}">
                <i class="fas fa-user-cog me-2"></i> Profile
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-danger-light"
               href="#"
               onclick="event.preventDefault(); document.getElementById('staff-logout-form').submit();">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
            <form id="staff-logout-form" action="{{ route('staff.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</nav>
