<?php
// File: routes/modules/enrollment.php

use App\Http\Controllers\Enrollment\EnrollmentController;
use App\Http\Controllers\Enrollment\DocumentController;
use App\Http\Controllers\Admin\EnrollmentManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Enrollment Routes Module
|--------------------------------------------------------------------------
| This file contains all routes related to the enrollment system
| including student enrollment verification, document uploads,
| and admin enrollment management.
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Student Enrollment Routes
|--------------------------------------------------------------------------
| Public routes for students to complete their enrollment process
| after passing the entry test.
|--------------------------------------------------------------------------
*/

Route::prefix('enrollment')->name('enrollment.')->group(function () {
    
    // Enrollment verification
    Route::get('/verify', [EnrollmentController::class, 'showVerificationForm'])->name('verify');
    Route::post('/verify', [EnrollmentController::class, 'verifyCode'])->name('verify.submit');
    
    // Document upload and management
    Route::get('/documents', [EnrollmentController::class, 'showDocumentForm'])->name('documents');
    Route::post('/documents/upload', [DocumentController::class, 'upload'])->name('documents.upload');
    Route::delete('/documents/{document}', [DocumentController::class, 'delete'])->name('documents.delete');
    Route::post('/documents/submit', [EnrollmentController::class, 'submitEnrollment'])->name('documents.submit');
    
    // Document viewing and downloading
    Route::get('/document/{document}/view', [DocumentController::class, 'view'])->name('document.view');
    Route::get('/document/{document}/download', [DocumentController::class, 'download'])->name('document.download');
    
    // Enrollment completion
    Route::get('/success', [EnrollmentController::class, 'showSuccess'])->name('success');
});

/*
|--------------------------------------------------------------------------
| Admin Enrollment Management Routes
|--------------------------------------------------------------------------
| Administrative routes for managing student enrollments and
| document reviews. Uses the same admin authentication as existing admin routes.
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth:admin'])->group(function () {
    
    // Main enrollment management
    Route::prefix('enrollments')->name('enrollments.')->group(function () {
        
        // Dashboard and student listings
        Route::get('/', [EnrollmentManagementController::class, 'index'])->name('index');
        
        // FIXED: Email configuration and testing routes MUST come BEFORE the catch-all /{student} route
        Route::get('/test-email', [EnrollmentManagementController::class, 'showEmailTest'])->name('test-email');
        Route::post('/test-email', [EnrollmentManagementController::class, 'sendTestEmail'])->name('test-email.send');
        
        // Bulk operations (also need to come before /{student} route)
        Route::post('/bulk/send-forms', [EnrollmentManagementController::class, 'bulkSendForms'])->name('bulk.send-forms');
        Route::post('/bulk/approve-documents', [EnrollmentManagementController::class, 'bulkApproveDocuments'])->name('bulk.approve-documents');
        
        // Document review actions (also need to come before /{student} route)
        Route::post('/document/{document}/approve', [EnrollmentManagementController::class, 'approveDocument'])->name('document.approve');
        Route::post('/document/{document}/reject', [EnrollmentManagementController::class, 'rejectDocument'])->name('document.reject');
        
        // IMPORTANT: This catch-all route MUST be LAST because it matches any string
        Route::get('/{student}', [EnrollmentManagementController::class, 'show'])->name('show');
        
        // Enrollment form management
        Route::post('/{student}/send-form', [EnrollmentManagementController::class, 'sendEnrollmentForm'])->name('send-form');
    });
    
    // Enhanced student attempts with enrollment actions
    // (This adds enrollment functionality to existing student attempts)
    Route::patch('student-attempts/{student}/send-enrollment', [EnrollmentManagementController::class, 'sendEnrollmentFromAttempts'])->name('student-attempts.send-enrollment');
});

/*
|--------------------------------------------------------------------------
| API Routes for AJAX Operations
|--------------------------------------------------------------------------
| These routes handle AJAX requests for real-time functionality
| like document validation and upload status checking.
|--------------------------------------------------------------------------
*/

Route::prefix('api/enrollment')->name('api.enrollment.')->group(function () {
    
    // Document validation before upload
    Route::post('/documents/validate', [DocumentController::class, 'validateDocument'])->name('documents.validate');
    
    // Get student upload status for progress tracking
    Route::get('/status/{student}', [EnrollmentController::class, 'getUploadStatus'])->name('status');
});