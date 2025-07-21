<?php
// File: routes/modules/courses.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\EnrollmentController;

// Course Routes
Route::prefix('courses')->name('courses.')->group(function () {
    // Public course listing
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('{course}', [CourseController::class, 'show'])->name('show');
    
    // Course enrollment (requires authentication)
    Route::middleware('auth')->group(function () {
        Route::post('{course}/enroll', [EnrollmentController::class, 'enroll'])->name('enroll');
        Route::get('my-courses', [EnrollmentController::class, 'myCourses'])->name('my-courses');
        Route::get('{course}/learn', [CourseController::class, 'learn'])->name('learn');
    });
});