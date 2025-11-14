<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Enrollment Details - {{ $student->full_name }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .top-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            margin: 0;
        }
        
        .top-banner .logo-section {
            display: table;
            width: 100%;
        }
        
        .top-banner .left-info,
        .top-banner .right-info {
            display: table-cell;
            vertical-align: middle;
        }
        
        .top-banner .left-info {
            width: 70%;
        }
        
        .top-banner .right-info {
            width: 30%;
            text-align: right;
        }
        
        .top-banner h1 {
            font-size: 32px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .top-banner .tagline {
            font-size: 14px;
            opacity: 0.95;
            margin-bottom: 12px;
        }
        
        .top-banner .contact-info {
            font-size: 10px;
            opacity: 0.9;
            margin-top: 8px;
        }
        
        .top-banner .doc-title {
            font-size: 18px;
            font-weight: bold;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 8px;
            display: inline-block;
        }
        
        .content-wrapper {
            padding: 20px 30px;
        }
        
        .header {
            text-align: center;
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .header h2 {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 11px;
            color: #6b7280;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #f3f4f6;
            padding: 10px;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: bold;
            border-left: 4px solid #667eea;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label,
        .info-value {
            display: table-cell;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-label {
            width: 35%;
            font-weight: bold;
            color: #6b7280;
            background: #f9fafb;
        }
        
        .info-value {
            width: 65%;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-passed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-failed {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .course-item {
            background: #f9fafb;
            padding: 10px;
            margin-bottom: 8px;
            border-left: 3px solid #667eea;
        }
        
        .course-item h4 {
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .course-badges {
            margin-top: 5px;
        }
        
        .course-badges span {
            display: inline-block;
            padding: 3px 8px;
            margin-right: 5px;
            background: #e0e7ff;
            border-radius: 8px;
            font-size: 9px;
        }
        
        .document-item {
            margin-bottom: 20px;
            page-break-inside: avoid;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background: #ffffff;
        }
        
        .document-item.approved {
            border-color: #10b981;
            background: #f0fdf4;
        }
        
        .document-item.rejected {
            border-color: #ef4444;
            background: #fef2f2;
        }
        
        .document-item.pending {
            border-color: #f59e0b;
            background: #fffbeb;
        }
        
        .document-header {
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .document-header strong {
            font-size: 12px;
            display: block;
            margin-bottom: 5px;
        }
        
        .document-image {
            text-align: center;
            margin: 15px 0;
        }
        
        .document-image img {
            max-width: 100%;
            max-height: 400px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
        }
        
        .document-meta {
            font-size: 10px;
            color: #6b7280;
            margin-top: 8px;
        }
        
        .document-meta p {
            margin: 3px 0;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            padding: 10px 0;
            border-top: 1px solid #e5e7eb;
            background: white;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Top Banner with IEHSAS Info - NO MARGIN -->
    <div class="top-banner">
        <div class="logo-section">
            <div class="left-info">
                <h1>IEHSAS</h1>
                <div class="tagline">Institute of Emerging Health Sciences & Advanced Studies</div>
                <div class="contact-info">
                    📧 info@iehsas.edu.pk | 📞 +92 300 1234567 | 🌐 www.iehsas.edu.pk<br>
                    📍 Lahore, Punjab, Pakistan
                </div>
            </div>
            <div class="right-info">
                <div class="doc-title">Enrollment Report</div>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <!-- Document Header -->
        <div class="header">
            <h2>Complete Student Enrollment Details</h2>
            <p>Official enrollment documentation for academic records</p>
        </div>

        <!-- Student Personal Information -->
        <div class="section">
            <div class="section-title">📋 Personal Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $student->full_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Father's Name</div>
                    <div class="info-value">{{ $student->father_name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $student->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $student->contact_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date of Birth</div>
                    <div class="info-value">{{ $student->date_of_birth ? $student->date_of_birth->format('F j, Y') : 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Gender</div>
                    <div class="info-value">{{ ucfirst($student->gender) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">{{ ucfirst($student->id_type) }}</div>
                    <div class="info-value">{{ $student->formatted_id }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nationality</div>
                    <div class="info-value">{{ $student->nationality ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Qualification</div>
                    <div class="info-value">{{ $student->qualification ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Home Address</div>
                    <div class="info-value">{{ $student->home_address ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Entry Test History -->
        @if($attempt)
        <div class="section">
            <div class="section-title">📝 Entry Test History</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Test Status</div>
                    <div class="info-value">
                        @if($attempt->status === 'completed')
                            @if($attempt->percentage >= $attempt->entryTest->passing_score)
                                <span class="status-badge status-passed">✓ PASSED</span>
                            @else
                                <span class="status-badge status-failed">✗ FAILED</span>
                            @endif
                        @else
                            <span class="status-badge status-pending">PENDING</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Score</div>
                    <div class="info-value">
                        <strong>{{ number_format($attempt->percentage, 2) }}%</strong>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Test Completed</div>
                    <div class="info-value">
                        {{ $attempt->completed_at ? $attempt->completed_at->format('F j, Y g:i A') : 'N/A' }}
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Duration</div>
                    <div class="info-value">
                        {{ $attempt->time_taken ? gmdate('H:i:s', $attempt->time_taken) : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Selected Courses -->
        @if($courses->count() > 0)
        <div class="section">
            <div class="section-title">🎓 Selected Courses ({{ $courses->count() }})</div>
            @foreach($courses as $selection)
                <div class="course-item">
                    <h4>{{ $selection->course->title }}</h4>
                    <div class="course-badges">
                        <span>Level: {{ ucfirst($selection->course->level ?? 'Beginner') }}</span>
                        @if($selection->course->duration)
                            <span>Duration: {{ $selection->course->duration }}</span>
                        @endif
                        @if($selection->course->start_date)
                            <span>Start: {{ $selection->course->start_date->format('M d, Y') }}</span>
                        @endif
                    </div>
                    @if($selection->course->description)
                        <p style="margin-top: 5px; font-size: 10px; color: #6b7280;">
                            {{ Str::limit($selection->course->description, 150) }}
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
        @else
        <div class="section">
            <div class="section-title">🎓 Selected Courses</div>
            <div class="no-data">No courses selected</div>
        </div>
        @endif

        <!-- Page Break for Documents -->
        <div class="page-break"></div>

        <!-- Submitted Documents with Images -->
        <div class="section">
            <div class="section-title">📄 Submitted Documents</div>
            
            @if(count($documents) > 0)
                @foreach($documents as $type => $typeDocuments)
                    <h3 style="font-size: 13px; margin: 20px 0 10px 0; color: #374151; font-weight: bold;">
                        {{ ucfirst($type) }} Documents
                    </h3>
                    
                    @foreach($typeDocuments as $subType => $document)
                        <div class="document-item {{ $document->status }}">
                            <div class="document-header">
                                <strong>
                                    {{ $document->sub_type_label ?: $document->document_type_label }}
                                    
                                    {{-- Status Badge --}}
                                    @if($document->status === 'approved')
                                        <span class="status-badge status-approved">✓ APPROVED</span>
                                    @elseif($document->status === 'rejected')
                                        <span class="status-badge status-rejected">✗ REJECTED</span>
                                    @else
                                        <span class="status-badge status-pending">⏳ UNDER REVIEW</span>
                                    @endif
                                </strong>
                            </div>
                            
                            {{-- Display Image if it's an image file --}}
                            @if($document->is_image)
                                <div class="document-image">
                                    <img src="{{ public_path('storage/' . $document->file_path) }}" alt="{{ $document->original_filename }}">
                                </div>
                            @else
                                {{-- For non-image files (PDF, DOC, etc) show file info --}}
                                <div style="text-align: center; padding: 40px; background: #f9fafb; border-radius: 8px; margin: 15px 0;">
                                    <div style="font-size: 48px; color: #9ca3af; margin-bottom: 10px;">📄</div>
                                    <p style="font-size: 12px; color: #4b5563; font-weight: bold; margin-bottom: 5px;">{{ $document->original_filename }}</p>
                                    <p style="font-size: 10px; color: #6b7280;">{{ $document->formatted_file_size }}</p>
                                    <p style="font-size: 10px; color: #9ca3af; margin-top: 8px;">Non-image document (PDF/DOC/DOCX)</p>
                                </div>
                            @endif
                            
                            {{-- Document Metadata --}}
                            <div class="document-meta">
                                <p>📎 <strong>Filename:</strong> {{ $document->original_filename }}</p>
                                <p>📏 <strong>Size:</strong> {{ $document->formatted_file_size }}</p>
                                <p>📅 <strong>Uploaded:</strong> {{ $document->uploaded_at->format('M d, Y g:i A') }}</p>
                                @if($document->reviewed_at)
                                    <p>✓ <strong>Reviewed:</strong> {{ $document->reviewed_at->format('M d, Y g:i A') }}</p>
                                @endif
                                @if($document->admin_comments)
                                    <p style="color: #dc2626; margin-top: 8px; font-weight: bold;">
                                        💬 <strong>Admin Comment:</strong> {{ $document->admin_comments }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
            @else
                <div class="no-data">No documents uploaded yet</div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Generated on {{ $generatedAt->format('F j, Y g:i A') }} | IEHSAS Enrollment System</p>
        <p>This is an automatically generated document - Official use only</p>
    </div>
</body>
</html>