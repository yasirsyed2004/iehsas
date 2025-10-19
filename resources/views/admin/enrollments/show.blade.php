{{-- resources/views/admin/enrollments/show.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment Details - {{ $student->full_name }} - Admin LMS</title>
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
                    <i class="fas fa-clipboard-check me-2"></i> Student Attempts
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
<div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Enrollment Details: {{ $student->full_name }}
                    </h1>
                    <p class="mt-2 text-gray-600">Review student information and documents</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.enrollments.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                        ← Back to Enrollments
                    </a>
                    @if($student->isEligibleForEnrollment())
                        <form method="POST" action="{{ route('admin.enrollments.send-form', $student) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                                    onclick="return confirm('Send enrollment form to {{ $student->email }}?')">
                                Send Enrollment Form
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Enrollment Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Enrollment Status</h3>
                <div class="flex items-center">
                    @php
                        $statusColors = [
                            'not_eligible' => 'bg-gray-100 text-gray-800',
                            'eligible' => 'bg-blue-100 text-blue-800',
                            'code_sent' => 'bg-yellow-100 text-yellow-800',
                            'code_expired' => 'bg-red-100 text-red-800',
                            'in_progress' => 'bg-orange-100 text-orange-800',
                            'completed' => 'bg-green-100 text-green-800',
                        ];
                        $statusColor = $statusColors[$student->enrollment_status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                        {{ $student->enrollment_status_label }}
                    </span>
                </div>
                @if($student->enrollment_code_generated_at)
                    <p class="text-sm text-gray-600 mt-2">
                        Code sent: {{ $student->enrollment_code_generated_at->format('M j, Y g:i A') }}
                    </p>
                @endif
            </div>

            <!-- Entry Test Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Entry Test</h3>
                @if($student->latestAttempt)
                    <div class="flex items-center">
                        @if($student->hasPassedEntryTest())
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Passed ({{ $student->latestAttempt->percentage }}%)
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                Failed ({{ $student->latestAttempt->percentage }}%)
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        Completed: {{ $student->latestAttempt->created_at->format('M j, Y') }}
                    </p>
                @else
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Not Attempted
                    </span>
                @endif
            </div>

            <!-- Documents Status -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Documents</h3>
                @if($student->enrollmentDocuments->count() > 0)
                    <div class="space-y-2">
                        @php $docStatus = $student->documents_status; @endphp
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                Approved: {{ $docStatus['approved'] ?? 0 }}
                            </span>
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">
                                Pending: {{ $docStatus['pending'] ?? 0 }}
                            </span>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">
                                Rejected: {{ $docStatus['rejected'] ?? 0 }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">
                            Total: {{ $docStatus['total'] ?? 0 }} documents
                        </p>
                    </div>
                @else
                    <span class="text-gray-400">No documents submitted</span>
                @endif
            </div>
        </div>

        <!-- Student Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Full Name</label>
                            <p class="text-gray-900">{{ $student->full_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Father's Name</label>
                            <p class="text-gray-900">{{ $student->father_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $student->email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone</label>
                            <p class="text-gray-900">{{ $student->contact_number }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Date of Birth</label>
                            <p class="text-gray-900">{{ $student->date_of_birth ? $student->date_of_birth->format('M j, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Gender</label>
                            <p class="text-gray-900">{{ ucfirst($student->gender) }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">{{ ucfirst($student->id_type) }}</label>
                            <p class="text-gray-900">{{ $student->formatted_id }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nationality</label>
                            <p class="text-gray-900">{{ $student->nationality ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Qualification</label>
                        <p class="text-gray-900">{{ $student->qualification }}</p>
                    </div>

                    @if($student->home_address)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Address</label>
                        <p class="text-gray-900">{{ $student->home_address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Entry Test Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Entry Test History</h3>
                </div>
                <div class="px-6 py-4">
                    @if($student->entryTestAttempts->count() > 0)
                        <div class="space-y-4">
                            @foreach($student->entryTestAttempts->take(5) as $attempt)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium">
                                            {{ $attempt->percentage }}%
                                            @if($attempt->percentage >= 60)
                                                <span class="text-green-600 text-sm">(Passed)</span>
                                            @else
                                                <span class="text-red-600 text-sm">(Failed)</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ $attempt->created_at->format('M j, Y g:i A') }}
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $attempt->status }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No entry test attempts found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        @if($student->enrollmentDocuments->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Submitted Documents</h3>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($student->enrollmentDocuments as $document)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $document->document_type_label }}</h4>
                                    <p class="text-sm text-gray-600">{{ $document->original_filename }}</p>
                                    <p class="text-xs text-gray-500">{{ $document->formatted_file_size }} • Uploaded {{ $document->uploaded_at->format('M j, Y') }}</p>
                                </div>
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $document->status_color }}">
                                    {{ $document->status_label }}
                                </span>
                            </div>

                            @if($document->admin_comments)
                                <div class="mb-3 p-2 bg-gray-50 rounded text-sm">
                                    <strong>Admin Comments:</strong> {{ $document->admin_comments }}
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ $document->file_url }}" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    View
                                </a>
                                <a href="{{ $document->download_url }}" 
                                   class="text-green-600 hover:text-green-800 text-sm">
                                    Download
                                </a>

                                @if($document->status === 'pending')
                                    <form method="POST" action="{{ route('admin.enrollments.document.approve', $document) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-800 text-sm"
                                                onclick="return confirm('Approve this document?')">
                                            Approve
                                        </button>
                                    </form>
                                    
                                    <button onclick="openRejectModal({{ $document->id }})" 
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        Reject
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
</div>

<!-- Reject Document Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Reject Document</h3>
                </div>
                <div class="px-6 py-4">
                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for rejection (required)
                    </label>
                    <textarea name="comments" 
                              id="comments" 
                              rows="4" 
                              required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Please provide specific feedback..."></textarea>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" 
                            onclick="closeRejectModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Reject Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(documentId) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectForm');
    form.action = `/admin/enrollments/document/${documentId}/reject`;
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    document.getElementById('comments').value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>