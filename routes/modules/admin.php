<?php
// File: routes/modules/admin.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EntryTestController as AdminEntryTestController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\StudentAttemptController;

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
    });
});