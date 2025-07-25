{{-- File: resources/views/admin/questions/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Bank - Admin LMS</title>
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
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .table-responsive {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .action-buttons .btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .question-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .question-text:hover {
            white-space: normal;
            overflow: visible;
        }
        .filter-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .options-preview {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .correct-answer {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 0.8rem;
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
                <a class="nav-link active" href="{{ route('admin.questions.index') }}">
                    <i class="fas fa-question-circle me-2"></i> Question Bank
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.student-attempts.index') }}">
                    <i class="fas fa-chart-line me-2"></i> Student Attempts
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
                <h2><i class="fas fa-question-circle me-2"></i>Question Bank Management</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Question Bank</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Question
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

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="h4 text-primary mb-1">{{ $questions->total() }}</div>
                    <div class="text-muted">Total Questions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="h4 text-success mb-1">{{ $questions->where('question_type', 'mcq')->count() }}</div>
                    <div class="text-muted">MCQ Questions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="h4 text-info mb-1">{{ $questions->where('question_type', 'true_false')->count() }}</div>
                    <div class="text-muted">True/False</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="h4 text-warning mb-1">{{ $entryTests->count() }}</div>
                    <div class="text-muted">Entry Tests</div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.questions.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Test</label>
                    <select class="form-select" name="entry_test">
                        <option value="">All Tests</option>
                        @foreach($entryTests as $test)
                            <option value="{{ $test->id }}" {{ request('entry_test') == $test->id ? 'selected' : '' }}>
                                {{ $test->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Question Type</label>
                    <select class="form-select" name="type">
                        <option value="">All Types</option>
                        <option value="mcq" {{ request('type') == 'mcq' ? 'selected' : '' }}>Multiple Choice</option>
                        <option value="true_false" {{ request('type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" placeholder="Search questions...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Questions Table -->
        <div class="table-responsive">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Questions List</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="selectAll()">
                        <i class="fas fa-check-square me-1"></i>Select All
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteSelected()" disabled id="delete-selected">
                        <i class="fas fa-trash me-1"></i>Delete Selected
                    </button>
                </div>
            </div>

            @if($questions->count() > 0)
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="select-all" class="form-check-input">
                            </th>
                            <th>ID</th>
                            <th>Entry Test</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Options</th>
                            <th>Marks</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input question-checkbox" 
                                           value="{{ $question->id }}">
                                </td>
                                <td class="fw-bold">#{{ $question->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $question->entryTest->title ?? 'N/A' }}</div>
                                    <small class="text-muted">
                                        {{ $question->entryTest->is_active ?? false ? 'Active' : 'Inactive' }}
                                    </small>
                                </td>
                                <td>
                                    <div class="question-text" title="{{ $question->question_text }}">
                                        {{ Str::limit($question->question_text, 60) }}
                                    </div>
                                </td>
                                <td>
                                    @if($question->question_type === 'mcq')
                                        <span class="badge bg-primary">MCQ</span>
                                    @else
                                        <span class="badge bg-info">True/False</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="options-preview">
                                        @if(is_array($question->options))
                                            @foreach(array_slice($question->options, 0, 2) as $index => $option)
                                                <div class="small">{{ chr(65 + $index) }}. {{ Str::limit($option, 20) }}</div>
                                            @endforeach
                                            @if(count($question->options) > 2)
                                                <div class="small text-muted">+{{ count($question->options) - 2 }} more</div>
                                            @endif
                                        @endif
                                        <div class="correct-answer mt-1">
                                            <i class="fas fa-check me-1"></i>{{ Str::limit($question->correct_answer, 15) }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $question->marks }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $question->order }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.questions.show', $question) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.questions.edit', $question) }}" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$question->answers()->exists())
                                            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this question?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-danger" 
                                                    title="Cannot delete - has answers" 
                                                    disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($questions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $questions->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Questions Found</h5>
                    <p class="text-muted">Start building your question bank by adding questions to your tests.</p>
                    <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add First Question
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select All functionality
        function selectAll() {
            const selectAllCheckbox = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.question-checkbox');
            const deleteButton = document.getElementById('delete-selected');
            
            selectAllCheckbox.checked = !selectAllCheckbox.checked;
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            updateDeleteButton();
        }

        // Individual checkbox listeners
        document.querySelectorAll('.question-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateDeleteButton);
        });

        function updateDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.question-checkbox:checked');
            const deleteButton = document.getElementById('delete-selected');
            
            if (checkedBoxes.length > 0) {
                deleteButton.disabled = false;
                deleteButton.textContent = `Delete Selected (${checkedBoxes.length})`;
            } else {
                deleteButton.disabled = true;
                deleteButton.innerHTML = '<i class="fas fa-trash me-1"></i>Delete Selected';
            }
        }

        // Delete selected questions
        function deleteSelected() {
            const checkedBoxes = document.querySelectorAll('.question-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                alert('Please select questions to delete.');
                return;
            }
            
            if (!confirm(`Are you sure you want to delete ${checkedBoxes.length} selected question(s)?`)) {
                return;
            }
            
            // Create form to submit selected IDs
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.questions.index") }}/bulk-delete';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add method
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            // Add selected IDs
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'question_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('show')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);

        // Search on Enter
        document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    </script>
</body>
</html>