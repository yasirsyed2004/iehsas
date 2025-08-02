<?php
// File: routes/modules/admin.php - UPDATED VERSION

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EntryTestController as AdminEntryTestController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\StudentAttemptController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseCategoryController;
use App\Http\Controllers\Admin\CourseModuleController;
use App\Http\Controllers\Admin\CourseLessonController;
use App\Http\Controllers\Admin\CourseEnrollmentController;

Route::prefix('admin')->name('admin.')->group(function () {
    // Public Admin Routes
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    // Protected Admin Routes
    Route::middleware(['auth:admin'])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        
        // Authentication
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        
        // User Management
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Entry Test Management
        Route::prefix('entry-tests')->name('entry-tests.')->group(function () {
            Route::get('/', [AdminEntryTestController::class, 'index'])->name('index');
            Route::get('create', [AdminEntryTestController::class, 'create'])->name('create');
            Route::post('/', [AdminEntryTestController::class, 'store'])->name('store');
            Route::get('{entryTest}', [AdminEntryTestController::class, 'show'])->name('show');
            Route::get('{entryTest}/edit', [AdminEntryTestController::class, 'edit'])->name('edit');
            Route::put('{entryTest}', [AdminEntryTestController::class, 'update'])->name('update');
            Route::delete('{entryTest}', [AdminEntryTestController::class, 'destroy'])->name('destroy');
            Route::post('{entryTest}/toggle-status', [AdminEntryTestController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // Question Bank Management
        Route::prefix('questions')->name('questions.')->group(function () {
            Route::get('/', [QuestionController::class, 'index'])->name('index');
            Route::get('create', [QuestionController::class, 'create'])->name('create');
            Route::post('/', [QuestionController::class, 'store'])->name('store');
            Route::get('{question}', [QuestionController::class, 'show'])->name('show');
            Route::get('{question}/edit', [QuestionController::class, 'edit'])->name('edit');
            Route::put('{question}', [QuestionController::class, 'update'])->name('update');
            Route::delete('{question}', [QuestionController::class, 'destroy'])->name('destroy');
        });
        
        // Student Attempts Management
        Route::prefix('student-attempts')->name('student-attempts.')->group(function () {
            Route::get('/', [StudentAttemptController::class, 'index'])->name('index');
            Route::get('{attempt}', [StudentAttemptController::class, 'show'])->name('show');
            Route::post('{attempt}/allow-retake', [StudentAttemptController::class, 'allowRetake'])->name('allow-retake');
            Route::delete('{attempt}', [StudentAttemptController::class, 'destroy'])->name('destroy');
        });

        // Course Categories Management
        Route::prefix('course-categories')->name('course-categories.')->group(function () {
            Route::get('/', [CourseCategoryController::class, 'index'])->name('index');
            Route::get('create', [CourseCategoryController::class, 'create'])->name('create');
            Route::post('/', [CourseCategoryController::class, 'store'])->name('store');
            Route::get('{courseCategory}', [CourseCategoryController::class, 'show'])->name('show');
            Route::get('{courseCategory}/edit', [CourseCategoryController::class, 'edit'])->name('edit');
            Route::put('{courseCategory}', [CourseCategoryController::class, 'update'])->name('update');
            Route::delete('{courseCategory}', [CourseCategoryController::class, 'destroy'])->name('destroy');
            Route::post('{courseCategory}/toggle-status', [CourseCategoryController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('reorder', [CourseCategoryController::class, 'reorder'])->name('reorder');
        });

        // Courses Management
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [CourseController::class, 'index'])->name('index');
            Route::get('create', [CourseController::class, 'create'])->name('create');
            Route::post('/', [CourseController::class, 'store'])->name('store');
            Route::get('{course}', [CourseController::class, 'show'])->name('show');
            Route::get('{course}/edit', [CourseController::class, 'edit'])->name('edit');
            Route::put('{course}', [CourseController::class, 'update'])->name('update');
            Route::delete('{course}', [CourseController::class, 'destroy'])->name('destroy');
            Route::post('{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('{course}/update-status', [CourseController::class, 'updateStatus'])->name('update-status');
            Route::post('{course}/duplicate', [CourseController::class, 'duplicate'])->name('duplicate');
            
            // Course Modules Management
            Route::prefix('{course}/modules')->name('modules.')->group(function () {
                Route::get('/', [CourseModuleController::class, 'index'])->name('index');
                Route::get('create', [CourseModuleController::class, 'create'])->name('create');
                Route::post('/', [CourseModuleController::class, 'store'])->name('store');
                Route::get('{module}', [CourseModuleController::class, 'show'])->name('show');
                Route::get('{module}/edit', [CourseModuleController::class, 'edit'])->name('edit');
                Route::put('{module}', [CourseModuleController::class, 'update'])->name('update');
                Route::delete('{module}', [CourseModuleController::class, 'destroy'])->name('destroy');
                Route::post('reorder', [CourseModuleController::class, 'reorder'])->name('reorder');
                
                // Course Lessons Management
                Route::prefix('{module}/lessons')->name('lessons.')->group(function () {
                    Route::get('/', [CourseLessonController::class, 'index'])->name('index');
                    Route::get('create', [CourseLessonController::class, 'create'])->name('create');
                    Route::post('/', [CourseLessonController::class, 'store'])->name('store');
                    Route::get('{lesson}', [CourseLessonController::class, 'show'])->name('show');
                    Route::get('{lesson}/edit', [CourseLessonController::class, 'edit'])->name('edit');
                    Route::put('{lesson}', [CourseLessonController::class, 'update'])->name('update');
                    Route::delete('{lesson}', [CourseLessonController::class, 'destroy'])->name('destroy');
                    Route::post('reorder', [CourseLessonController::class, 'reorder'])->name('reorder');
                });
            });
            
            // Course Enrollments Management
            Route::prefix('{course}/enrollments')->name('enrollments.')->group(function () {
                Route::get('/', [CourseEnrollmentController::class, 'index'])->name('index');
                Route::post('enroll', [CourseEnrollmentController::class, 'enrollStudent'])->name('enroll');
                Route::post('{enrollment}/update-status', [CourseEnrollmentController::class, 'updateStatus'])->name('update-status');
                Route::delete('{enrollment}', [CourseEnrollmentController::class, 'destroy'])->name('destroy');
            });
        });

        // Bulk Course Enrollments
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [CourseEnrollmentController::class, 'allEnrollments'])->name('index');
            Route::get('bulk-enroll', [CourseEnrollmentController::class, 'bulkEnrollForm'])->name('bulk-enroll');
            Route::post('bulk-enroll', [CourseEnrollmentController::class, 'bulkEnroll'])->name('bulk-enroll.store');
            Route::get('export', [CourseEnrollmentController::class, 'export'])->name('export');
        });
    });
});