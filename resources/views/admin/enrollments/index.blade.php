{{-- File: resources/views/admin/enrollments/index.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management - Admin LMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Enrollment Management</h1>
                    <p class="mt-2 text-gray-600">Manage student enrollments and document reviews</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.enrollments.test-email') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Test Email
                    </a>
                    <button onclick="bulkSendForms()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Send Bulk Forms
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Eligible Students</p>
                        <p class="text-3xl font-bold">{{ $stats['eligible'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Code Sent</p>
                        <p class="text-3xl font-bold">{{ $stats['code_sent'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium">In Progress</p>
                        <p class="text-3xl font-bold">{{ $stats['in_progress'] ?? 0 }}</p>
                    </div>
                    <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold">{{ $stats['completed'] ?? 0 }}</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('admin.enrollments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Students</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Name, email, or phone..."
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Entry Test Filter -->
                <div>
                    <label for="entry_test" class="block text-sm font-medium text-gray-700 mb-1">Entry Test</label>
                    <select id="entry_test" 
                            name="entry_test" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Tests</option>
                        <!-- Add entry test options here if needed -->
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Enrollment Status</label>
                    <select id="status" 
                            name="status" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="eligible" {{ request('status') === 'eligible' ? 'selected' : '' }}>Eligible</option>
                        <option value="code_sent" {{ request('status') === 'code_sent' ? 'selected' : '' }}>Code Sent</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end space-x-2">
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.enrollments.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Students</h3>
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                        <label for="selectAll" class="text-sm text-gray-600">Select All</label>
                    </div>
                </div>
            </div>

            @if (isset($students) && $students->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" class="rounded border-gray-300">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entry Test</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($students as $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" 
                                               class="student-checkbox rounded border-gray-300" 
                                               value="{{ $student->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ substr($student->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $student->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $student->entry_test_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ ucfirst($student->enrollment_status ?? 'eligible') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if (isset($student->documents_status))
                                            <div class="flex space-x-2">
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Approved: {{ $student->documents_status['approved'] ?? 0 }}</span>
                                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Pending: {{ $student->documents_status['pending'] ?? 0 }}</span>
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Rejected: {{ $student->documents_status['rejected'] ?? 0 }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">Not submitted</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.enrollments.show', $student) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        
                                        @if (method_exists($student, 'isEligibleForEnrollment') && $student->isEligibleForEnrollment())
                                            <form method="POST" action="{{ route('admin.enrollments.send-form', $student) }}" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900"
                                                        onclick="return confirm('Send enrollment form to {{ $student->email }}?')">
                                                    Send Form
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $students->appends(request()->query())->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No students found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a student or adjusting your filters.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript for bulk operations -->
    <script>
        function bulkSendForms() {
            const selectedStudents = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
            
            if (selectedStudents.length === 0) {
                alert('Please select at least one student.');
                return;
            }
            
            if (confirm(`Send enrollment forms to ${selectedStudents.length} selected students?`)) {
                // Implementation for bulk send forms
                fetch('{{ route("admin.enrollments.bulk.send-forms") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        student_ids: selectedStudents
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Enrollment forms sent successfully!');
                        location.reload();
                    } else {
                        alert('Error sending forms: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending forms.');
                });
            }
        }

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.student-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>