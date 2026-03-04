{{-- File: resources/views/staff/notifications/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - IEHSAS Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @include('staff.partials.styles')
</head>
<body>
    @include('staff.partials.sidebar', ['activeMenu' => 'notifications'])

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-bell me-2"></i>Notifications</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Notifications</li>
                    </ol>
                </nav>
            </div>
            <form action="{{ route('staff.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-check-double me-1"></i>Mark All Read
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                        <div class="list-group-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary me-2">New</span>
                                        @endif
                                        <h6 class="mb-1">{{ $notification->title }}</h6>
                                    </div>
                                    <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    @if(!$notification->is_read)
                                    <form action="{{ route('staff.notifications.mark-read', $notification) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as read">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if($notification->data && isset($notification->data['task_id']))
                                        <a href="{{ route('staff.tasks.show', $notification->data['task_id']) }}" class="btn btn-sm btn-outline-primary" title="View Task">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No notifications</h4>
                        <p class="text-muted">You're all caught up!</p>
                    </div>
                @endif
            </div>
        </div>

        @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
