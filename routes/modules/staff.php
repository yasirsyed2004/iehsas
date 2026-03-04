<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\AuthController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\TaskController;
use App\Http\Controllers\Staff\NotificationController;
use App\Http\Controllers\Staff\DarController;

Route::prefix('staff')->name('staff.')->group(function () {
    // Public Staff Routes
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');

    // Protected Staff Routes
    Route::middleware(['staff.auth'])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Authentication
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('profile', [AuthController::class, 'updateProfile'])->name('profile.update');

        // My Tasks
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [TaskController::class, 'index'])->name('index');
            Route::get('{task}', [TaskController::class, 'show'])->name('show');
            Route::post('{task}/submit-progress', [TaskController::class, 'submitProgress'])->name('submit-progress');
            Route::post('{task}/submit-for-review', [TaskController::class, 'submitForReview'])->name('submit-for-review');
            Route::post('{task}/comment', [TaskController::class, 'addComment'])->name('add-comment');
        });

        // Daily Activity Reports (DAR)
        Route::prefix('dar')->name('dar.')->group(function () {
            Route::get('/', [DarController::class, 'index'])->name('index');
            Route::get('create', [DarController::class, 'create'])->name('create');
            Route::post('/', [DarController::class, 'store'])->name('store');
            Route::get('{dar}', [DarController::class, 'show'])->name('show');
            Route::get('{dar}/edit', [DarController::class, 'edit'])->name('edit');
            Route::put('{dar}', [DarController::class, 'update'])->name('update');
            Route::post('{dar}/submit', [DarController::class, 'submit'])->name('submit');
            Route::post('{dar}/addendum', [DarController::class, 'addAddendum'])->name('add-addendum');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::post('{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        });
    });
});
