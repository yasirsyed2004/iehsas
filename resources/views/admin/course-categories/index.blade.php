{{-- File: resources/views/admin/course-categories/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Categories Management - Admin Dashboard</title>
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
        .category-card {
            transition: all 0.3s;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .category-header {
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .category-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(20px, -20px);
        }
        .category-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .category-body {
            padding: 20px;
            background: white;
        }
        .stats-badge {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-right: 8px;
        }
        .drag-handle {
            cursor: move;
            color: #6c757d;
            padding: 10px;
        }
        .drag-handle:hover {
            color: #495057;
        }
        .sortable-ghost {
            opacity: 0.4;
        }
        .sortable-chosen {
            box-shadow: 0 0 15px rgba(52, 152, 219, 0.5);
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .action-buttons .btn {
            margin: 0 2px;
            padding: 6px 12px;
            border-radius: 6px;
        }
        .color-swatch {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px rgba(0,0,0,0.1);
            display: inline-block;
            margin-right: 8px;
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
                <a class="nav-link active" href="{{ route('admin.course-categories.index') }}">
                    <i class="fas fa-tags me-2"></i> Course Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.enrollments.index') }}">
                    <i class="fas fa-user-graduate me-2"></i> Enrollments
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
                <h2><i class="fas fa-tags me-2"></i>Course Categories</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Course Categories</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-info" onclick="toggleView()">
                    <i class="fas fa-th-large me-1" id="view-icon"></i>
                    <span id="view-text">Card View</span>
                </button>
                <a href="{{ route('admin.course-categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add New Category
                </a>
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

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ $categories->total() }}</h3>
                                <p class="mb-0">Total Categories</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-tags fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ $categories->where('is_active', true)->count() }}</h3>
                                <p class="mb-0">Active Categories</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ $categories->sum('courses_count') }}</h3>
                                <p class="mb-0">Total Courses</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-graduation-cap fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ $categories->where('is_active', false)->count() }}</h3>
                                <p class="mb-0">Inactive Categories</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-eye-slash fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card View -->
        <div id="card-view" class="row" style="display: none;">
            @forelse($categories as $category)
                <div class="col-lg-4 col-md-6 mb-4" data-category-id="{{ $category->id }}">
                    <div class="category-card">
                        <div class="category-header" style="background: {{ $category->color ?? '#6c757d' }};">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="category-icon">
                                        <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                                    </div>
                                    <h5 class="mb-1">{{ $category->name }}</h5>
                                    <p class="mb-2 opacity-90">{{ Str::limit($category->description, 80) }}</p>
                                </div>
                                <div class="text-end">
                                    @if(!$category->is_active)
                                        <span class="badge bg-light text-dark">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3">
                                <span class="stats-badge">
                                    <i class="fas fa-graduation-cap me-1"></i>{{ $category->courses_count }} courses
                                </span>
                                <span class="stats-badge">
                                    <i class="fas fa-sort me-1"></i>Order: {{ $category->sort_order }}
                                </span>
                            </div>
                        </div>
                        <div class="category-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Created {{ $category->created_at->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.course-categories.show', $category) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.course-categories.edit', $category) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.course-categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $category->is_active ? 'danger' : 'success' }}" title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-toggle-{{ $category->is_active ? 'off' : 'on' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No categories found</h4>
                        <p class="text-muted">Create your first course category to get started.</p>
                        <a href="{{ route('admin.course-categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create Category
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Table View -->
        <div id="table-view">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tags me-2"></i>All Categories ({{ $categories->total() }})
                        </h5>
                        <div class="text-muted">
                            <small><i class="fas fa-arrows-alt me-1"></i>Drag to reorder</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="categories-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">Order</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Courses</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-categories">
                                    @foreach($categories as $category)
                                    <tr data-id="{{ $category->id }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="drag-handle me-2">
                                                    <i class="fas fa-grip-vertical"></i>
                                                </span>
                                                <span class="badge bg-secondary">{{ $category->sort_order }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($category->color)
                                                    <span class="color-swatch" style="background-color: {{ $category->color }};"></span>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">
                                                        @if($category->icon)
                                                            <i class="{{ $category->icon }} me-2"></i>
                                                        @endif
                                                        {{ $category->name }}
                                                    </h6>
                                                    <small class="text-muted">{{ $category->slug }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ Str::limit($category->description, 100) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-graduation-cap me-2 text-primary"></i>
                                                <span class="fw-bold">{{ $category->courses_count }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $category->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.course-categories.show', $category) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.course-categories.edit', $category) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.course-categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $category->is_active ? 'danger' : 'success' }}" title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-toggle-{{ $category->is_active ? 'off' : 'on' }}"></i>
                                                    </button>
                                                </form>
                                                @if($category->courses_count == 0)
                                                <form action="{{ route('admin.course-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No categories found</h4>
                            <p class="text-muted">Create your first course category to get started.</p>
                            <a href="{{ route('admin.course-categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create Category
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // View toggle functionality
        let currentView = 'table';

        function toggleView() {
            const cardView = document.getElementById('card-view');
            const tableView = document.getElementById('table-view');
            const viewIcon = document.getElementById('view-icon');
            const viewText = document.getElementById('view-text');

            if (currentView === 'table') {
                // Switch to card view
                tableView.style.display = 'none';
                cardView.style.display = 'flex';
                viewIcon.className = 'fas fa-list me-1';
                viewText.textContent = 'Table View';
                currentView = 'card';
            } else {
                // Switch to table view
                cardView.style.display = 'none';
                tableView.style.display = 'block';
                viewIcon.className = 'fas fa-th-large me-1';
                viewText.textContent = 'Card View';
                currentView = 'table';
            }
        }

        // Initialize sortable functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.getElementById('sortable-categories');
            if (tbody) {
                new Sortable(tbody, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function(evt) {
                        // Update sort order
                        updateSortOrder();
                    }
                });
            }
        });

        function updateSortOrder() {
            const rows = document.querySelectorAll('#sortable-categories tr');
            const updates = [];
            
            rows.forEach((row, index) => {
                const categoryId = row.dataset.id;
                const newOrder = index + 1;
                
                // Update visual order badge
                const orderBadge = row.querySelector('.badge');
                if (orderBadge) {
                    orderBadge.textContent = newOrder;
                }
                
                updates.push({
                    id: categoryId,
                    sort_order: newOrder
                });
            });

            // Send AJAX request to update order
            fetch('{{ route("admin.course-categories.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ categories: updates })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', 'Categories reordered successfully!');
                } else {
                    // Show error message
                    showAlert('error', 'Failed to reorder categories.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while reordering categories.');
            });
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.main-content');
            const firstChild = container.firstElementChild;
            container.insertBefore(alertDiv, firstChild.nextSibling);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }

        // Add CSRF token to page if not already present
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const metaTag = document.createElement('meta');
            metaTag.name = 'csrf-token';
            metaTag.content = '{{ csrf_token() }}';
            document.head.appendChild(metaTag);
        }
    </script>
</body>
</html>