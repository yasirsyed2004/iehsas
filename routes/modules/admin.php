<?php
// File: routes/modules/admin.php

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
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\RegisteredStudentController;
use App\Http\Controllers\Admin\DarController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\RolePermissionController;

Route::prefix('admin')->name('admin.')->group(function () {
    // Public Admin Routes
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    // Protected Admin Routes
    Route::middleware(['auth:admin'])->group(function () {
        // Dashboard (accessible to all authenticated admins — it's the landing page)
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Authentication (no permission needed — all admins need these)
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('profile', [AuthController::class, 'updateProfile'])->name('profile.update');

        // User Management
        Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('can:view_users');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('can:create_users');
        Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('can:create_users');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('can:view_users');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('can:edit_users');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('can:edit_users');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('can:edit_users');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:delete_users');

        // Entry Test Management
        Route::prefix('entry-tests')->name('entry-tests.')->group(function () {
            Route::get('/', [AdminEntryTestController::class, 'index'])->name('index')->middleware('can:view_entry_tests');
            Route::get('create', [AdminEntryTestController::class, 'create'])->name('create')->middleware('can:create_entry_tests');
            Route::post('/', [AdminEntryTestController::class, 'store'])->name('store')->middleware('can:create_entry_tests');
            Route::get('{entryTest}', [AdminEntryTestController::class, 'show'])->name('show')->middleware('can:view_entry_tests');
            Route::get('{entryTest}/edit', [AdminEntryTestController::class, 'edit'])->name('edit')->middleware('can:edit_entry_tests');
            Route::put('{entryTest}', [AdminEntryTestController::class, 'update'])->name('update')->middleware('can:edit_entry_tests');
            Route::post('{entryTest}/toggle-status', [AdminEntryTestController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:edit_entry_tests');
            Route::delete('{entryTest}', [AdminEntryTestController::class, 'destroy'])->name('destroy')->middleware('can:delete_entry_tests');
        });

        // Question Bank Management
        Route::prefix('questions')->name('questions.')->group(function () {
            Route::get('/', [QuestionController::class, 'index'])->name('index')->middleware('can:view_questions');
            Route::get('create', [QuestionController::class, 'create'])->name('create')->middleware('can:create_questions');
            Route::post('/', [QuestionController::class, 'store'])->name('store')->middleware('can:create_questions');
            Route::get('{question}', [QuestionController::class, 'show'])->name('show')->middleware('can:view_questions');
            Route::get('{question}/edit', [QuestionController::class, 'edit'])->name('edit')->middleware('can:edit_questions');
            Route::put('{question}', [QuestionController::class, 'update'])->name('update')->middleware('can:edit_questions');
            Route::delete('{question}', [QuestionController::class, 'destroy'])->name('destroy')->middleware('can:delete_questions');
        });

        // Student Attempts Management
        Route::prefix('student-attempts')->name('student-attempts.')->middleware('can:view_student_attempts')->group(function () {
            Route::get('/', [StudentAttemptController::class, 'index'])->name('index');
            Route::get('{attempt}', [StudentAttemptController::class, 'show'])->name('show');
            Route::post('{attempt}/allow-retake', [StudentAttemptController::class, 'allowRetake'])->name('allow-retake')->middleware('can:manage_student_attempts');
            Route::delete('{attempt}', [StudentAttemptController::class, 'destroy'])->name('destroy')->middleware('can:manage_student_attempts');
        });

        // Exam Screenshot Viewer (for admin)
        Route::get('exam-screenshot/{screenshot}', function (\App\Models\ExamScreenshot $screenshot) {
            $path = $screenshot->file_path;
            if (!\Illuminate\Support\Facades\Storage::disk('enrollment')->exists($path)) {
                abort(404);
            }
            return response(\Illuminate\Support\Facades\Storage::disk('enrollment')->get($path), 200, [
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'private, max-age=3600',
            ]);
        })->name('exam-screenshot')->middleware('can:view_student_attempts');

        // Registered Students Management
        Route::prefix('registered-students')->name('registered-students.')->middleware('can:view_registered_students')->group(function () {
            Route::get('/', [RegisteredStudentController::class, 'index'])->name('index');
            Route::get('export', [RegisteredStudentController::class, 'export'])->name('export');
            Route::get('{student}', [RegisteredStudentController::class, 'show'])->name('show');
            Route::post('{student}/toggle-retake', [RegisteredStudentController::class, 'toggleRetake'])->name('toggle-retake')->middleware('can:manage_registered_students');
            Route::delete('{student}', [RegisteredStudentController::class, 'destroy'])->name('destroy')->middleware('can:manage_registered_students');
        });

        // Course Categories Management
        Route::prefix('course-categories')->name('course-categories.')->group(function () {
            Route::get('/', [CourseCategoryController::class, 'index'])->name('index')->middleware('can:view_course_categories');
            Route::get('create', [CourseCategoryController::class, 'create'])->name('create')->middleware('can:create_course_categories');
            Route::post('/', [CourseCategoryController::class, 'store'])->name('store')->middleware('can:create_course_categories');
            Route::post('reorder', [CourseCategoryController::class, 'reorder'])->name('reorder')->middleware('can:edit_course_categories');
            Route::get('{courseCategory}', [CourseCategoryController::class, 'show'])->name('show')->middleware('can:view_course_categories');
            Route::get('{courseCategory}/edit', [CourseCategoryController::class, 'edit'])->name('edit')->middleware('can:edit_course_categories');
            Route::put('{courseCategory}', [CourseCategoryController::class, 'update'])->name('update')->middleware('can:edit_course_categories');
            Route::post('{courseCategory}/toggle-status', [CourseCategoryController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:edit_course_categories');
            Route::delete('{courseCategory}', [CourseCategoryController::class, 'destroy'])->name('destroy')->middleware('can:delete_course_categories');
        });

        // Courses Management
        Route::prefix('courses')->name('courses.')->group(function () {
            Route::get('/', [CourseController::class, 'index'])->name('index')->middleware('can:view_courses');
            Route::get('create', [CourseController::class, 'create'])->name('create')->middleware('can:create_courses');
            Route::post('/', [CourseController::class, 'store'])->name('store')->middleware('can:create_courses');
            Route::get('{course}', [CourseController::class, 'show'])->name('show')->middleware('can:view_courses');
            Route::get('{course}/edit', [CourseController::class, 'edit'])->name('edit')->middleware('can:edit_courses');
            Route::put('{course}', [CourseController::class, 'update'])->name('update')->middleware('can:edit_courses');
            Route::post('{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:edit_courses');
            Route::post('{course}/update-status', [CourseController::class, 'updateStatus'])->name('update-status')->middleware('can:edit_courses');
            Route::post('{course}/duplicate', [CourseController::class, 'duplicate'])->name('duplicate')->middleware('can:edit_courses');
            Route::delete('{course}', [CourseController::class, 'destroy'])->name('destroy')->middleware('can:delete_courses');

            // Course Modules Management (inherits course permission)
            Route::prefix('{course}/modules')->name('modules.')->middleware('can:edit_courses')->group(function () {
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
            Route::prefix('{course}/enrollments')->name('enrollments.')->middleware('can:view_enrollments')->group(function () {
                Route::get('/', [CourseEnrollmentController::class, 'index'])->name('index');
                Route::post('enroll', [CourseEnrollmentController::class, 'enrollStudent'])->name('enroll')->middleware('can:manage_enrollments');
                Route::post('{enrollment}/update-status', [CourseEnrollmentController::class, 'updateStatus'])->name('update-status')->middleware('can:manage_enrollments');
                Route::delete('{enrollment}', [CourseEnrollmentController::class, 'destroy'])->name('destroy')->middleware('can:manage_enrollments');
            });
        });

        // Bulk Course Enrollments
        Route::prefix('enrollments')->name('enrollments.')->middleware('can:view_enrollments')->group(function () {
            Route::get('/', [CourseEnrollmentController::class, 'allEnrollments'])->name('index');
            Route::get('export', [CourseEnrollmentController::class, 'export'])->name('export');
            Route::middleware('can:manage_enrollments')->group(function () {
                Route::get('bulk-enroll', [CourseEnrollmentController::class, 'bulkEnrollForm'])->name('bulk-enroll');
                Route::post('bulk-enroll', [CourseEnrollmentController::class, 'bulkEnroll'])->name('bulk-enroll.store');
            });
        });

        // Department Management
        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('index')->middleware('can:view_departments');
            Route::get('create', [DepartmentController::class, 'create'])->name('create')->middleware('can:create_departments');
            Route::post('/', [DepartmentController::class, 'store'])->name('store')->middleware('can:create_departments');
            Route::get('{department}', [DepartmentController::class, 'show'])->name('show')->middleware('can:view_departments');
            Route::get('{department}/edit', [DepartmentController::class, 'edit'])->name('edit')->middleware('can:edit_departments');
            Route::put('{department}', [DepartmentController::class, 'update'])->name('update')->middleware('can:edit_departments');
            Route::post('{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('toggle-status')->middleware('can:edit_departments');
            Route::delete('{department}', [DepartmentController::class, 'destroy'])->name('destroy')->middleware('can:delete_departments');
        });

        // Task Management
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [TaskController::class, 'index'])->name('index')->middleware('can:view_tasks');
            Route::get('create', [TaskController::class, 'create'])->name('create')->middleware('can:create_tasks');
            Route::get('export-pdf', [ExportController::class, 'taskSummaryPdf'])->name('export-pdf')->middleware('can:view_tasks');
            Route::get('export-excel', [ExportController::class, 'taskSummaryExcel'])->name('export-excel')->middleware('can:view_tasks');
            Route::post('/', [TaskController::class, 'store'])->name('store')->middleware('can:create_tasks');
            Route::get('{task}', [TaskController::class, 'show'])->name('show')->middleware('can:view_tasks');
            Route::get('{task}/edit', [TaskController::class, 'edit'])->name('edit')->middleware('can:edit_tasks');
            Route::get('{task}/export-pdf', [ExportController::class, 'taskDetailPdf'])->name('detail-pdf')->middleware('can:view_tasks');
            Route::put('{task}', [TaskController::class, 'update'])->name('update')->middleware('can:edit_tasks');
            Route::post('{task}/comment', [TaskController::class, 'addComment'])->name('add-comment')->middleware('can:edit_tasks');
            Route::delete('{task}/comment/{comment}', [TaskController::class, 'deleteComment'])->name('delete-comment')->middleware('can:edit_tasks');
            Route::delete('{task}/attachment/{attachment}', [TaskController::class, 'deleteAttachment'])->name('delete-attachment')->middleware('can:edit_tasks');
            Route::post('{task}/generate-share', [TaskController::class, 'generateShareLink'])->name('generate-share')->middleware('can:edit_tasks');
            Route::post('{task}/revoke-share', [TaskController::class, 'revokeShareLink'])->name('revoke-share')->middleware('can:edit_tasks');
            Route::post('{task}/update-progress', [TaskController::class, 'updateProgress'])->name('update-progress')->middleware('can:edit_tasks');
            Route::post('{task}/review', [TaskController::class, 'submitReview'])->name('submit-review')->middleware('can:edit_tasks');
            Route::delete('{task}', [TaskController::class, 'destroy'])->name('destroy')->middleware('can:delete_tasks');
        });

        // Daily Activity Reports (DAR) Management
        Route::prefix('dar')->name('dar.')->middleware('can:view_dar')->group(function () {
            Route::get('/', [DarController::class, 'index'])->name('index');
            Route::get('monthly', [DarController::class, 'monthly'])->name('monthly');
            Route::get('monthly/export-pdf', [ExportController::class, 'darMonthlyPdf'])->name('monthly-pdf');
            Route::get('monthly/export-excel', [ExportController::class, 'darMonthlyExcel'])->name('monthly-excel');
            Route::get('{dar}', [DarController::class, 'show'])->name('show');
            Route::post('{dar}/review', [DarController::class, 'review'])->name('review')->middleware('can:manage_dar');
            Route::get('{dar}/export-pdf', [ExportController::class, 'darDetailPdf'])->name('detail-pdf');
        });

        // Notifications (all admins can view their own notifications)
        Route::prefix('notifications')->name('notifications.')->middleware('can:view_notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('recent', [NotificationController::class, 'getRecent'])->name('recent');
            Route::post('{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('{notification}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::delete('/', [NotificationController::class, 'clearAll'])->name('clear-all');
        });

        // Roles & Permissions Management
        Route::prefix('roles')->name('roles.')->middleware('can:manage_roles_permissions')->group(function () {
            Route::get('/', [RolePermissionController::class, 'index'])->name('index');
            Route::get('create', [RolePermissionController::class, 'create'])->name('create');
            Route::post('/', [RolePermissionController::class, 'store'])->name('store');
            Route::get('{role}/edit', [RolePermissionController::class, 'edit'])->name('edit');
            Route::put('{role}', [RolePermissionController::class, 'update'])->name('update');
            Route::delete('{role}', [RolePermissionController::class, 'destroy'])->name('destroy');
            Route::post('assign-role/{admin}', [RolePermissionController::class, 'assignRole'])->name('assign-role');

            // Admin Account Management
            Route::get('admins/create', [RolePermissionController::class, 'createAdmin'])->name('admins.create');
            Route::post('admins', [RolePermissionController::class, 'storeAdmin'])->name('admins.store');
            Route::get('admins/{admin}/edit', [RolePermissionController::class, 'editAdmin'])->name('admins.edit');
            Route::put('admins/{admin}', [RolePermissionController::class, 'updateAdmin'])->name('admins.update');
            Route::delete('admins/{admin}', [RolePermissionController::class, 'destroyAdmin'])->name('admins.destroy');
            Route::post('admins/{admin}/toggle-status', [RolePermissionController::class, 'toggleAdminStatus'])->name('admins.toggle-status');
        });
    });
});

// Public route for task sharing (outside admin middleware)
Route::get('task/view/{token}', [TaskController::class, 'publicView'])->name('task.public-view');
