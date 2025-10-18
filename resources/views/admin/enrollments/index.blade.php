@extends('layouts.app')

@section('title', 'Enrollment Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
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
                    <p class="text-3xl font-bold">{{ $stats['eligible'] }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium">Code Sent</p>
                    <p class="text-3xl font-bold">{{ $stats['code_sent'] }}</p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">In Progress</p>
                    <p class="text-3xl font-bold">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Completed</p>
                    <p class="text-3xl font-bold">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="flex flex-wrap gap-4">
            
            <!-- Search -->
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Students</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search by name, email, or ID number..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Status Filter -->
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Enrollment Status</label>
                <select name="status" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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

        @if ($students->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Test Result</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollment Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documents</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($students as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" 
                                           name="student_ids[]" 
                                           value="{{ $student->id }}" 
                                           class="student-checkbox rounded border-gray-300">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $student->id_type_label }}: {{ $student->formatted_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $student->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->contact_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($student->latestAttempt)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $student->latestAttempt->status === 'pass' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($student->latestAttempt->status) }}
                                        </span>
                                        <div class="text-xs text-gray-500">
                                            {{ $student->latestAttempt->score ?? 'N/A' }}/{{ $student->latestAttempt->total_questions ?? 'N/A' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">No attempts</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @switch($student->enrollment_status)
                                            @case('eligible') bg-blue-100 text-blue-800 @break
                                            @case('code_sent') bg-yellow-100 text-yellow-800 @break
                                            @case('in_progress') bg-purple-100 text-purple-800 @break
                                            @case('completed') bg-green-100 text-green-800 @break
                                            @case('code_expired') bg-red-100 text-red-800 @break
                                            @default bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ $student->enrollment_status_label }}
                                    </span>
                                    @if ($student->hasEnrollmentCodeBeenSent())
                                        <div class="text-xs text-gray-500 mt-1">
                                            Code: {{ $student->enrollment_verification_code }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($student->enrollment_form_submitted)
                                        @php $docStatus = $student->getDocumentStatusSummary(); @endphp
                                        <div class="flex space-x-1">
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">{{ $docStatus['approved'] }}</span>
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">{{ $docStatus['pending'] }}</span>
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">{{ $docStatus['rejected'] }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">Not submitted</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.enrollments.show', $student) }}" 
                                       class="text-blue-600 hover:text-blue-900">View</a>
                                    
                                    @if ($student->isEligibleForEnrollment())
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No students found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria or filters.</p>
            </div>
        @endif
    </div>
</div>

<script>
// Bulk selection functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
        const allChecked = studentCheckboxes.length > 0 && checkedBoxes.length === studentCheckboxes.length;
        const someChecked = checkedBoxes.length > 0;
        
        if (selectAll) {
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        }
        if (selectAllHeader) {
            selectAllHeader.checked = allChecked;
            selectAllHeader.indeterminate = someChecked && !allChecked;
        }
    }

    function toggleAll(checked) {
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            toggleAll(this.checked);
        });
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            toggleAll(this.checked);
        });
    }

    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllState);
    });

    updateSelectAllState();
});

function bulkSendForms() {
    const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Please select at least one student.');
        return;
    }

    if (!confirm(`Send enrollment forms to ${checkedBoxes.length} selected students?`)) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.enrollments.bulk.send-forms") }}';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.content;
        form.appendChild(csrfInput);
    }

    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection