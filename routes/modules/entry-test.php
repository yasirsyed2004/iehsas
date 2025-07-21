<?php
// File: routes/modules/entry-test.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntryTest\EntryTestController;
use App\Http\Controllers\EntryTest\StudentRegistrationController;

// Entry Test Routes
Route::prefix('entry-test')->name('entry-test.')->group(function () {
    // Introduction page (moved from dashboard)
    Route::get('introduction', [EntryTestController::class, 'introduction'])->name('introduction');
    
    // Student registration and test information
    Route::get('/', [EntryTestController::class, 'index'])->name('index');
    Route::get('register', [StudentRegistrationController::class, 'showForm'])->name('register');
    Route::post('register', [StudentRegistrationController::class, 'store'])->name('register.submit');
    
    // Test flow
    Route::get('instructions/{entryTest}', [EntryTestController::class, 'instructions'])->name('instructions');
    Route::post('start/{entryTest}', [EntryTestController::class, 'start'])->name('start');
    Route::get('take/{entryTest}/{attempt}', [EntryTestController::class, 'take'])->name('take');
    
    // Test interaction
    Route::post('attempt/{attempt}/answer', [EntryTestController::class, 'submitAnswer'])->name('submit-answer');
    Route::post('attempt/{attempt}/submit', [EntryTestController::class, 'submit'])->name('submit');
    Route::post('attempt/{attempt}/violation', [EntryTestController::class, 'trackViolation'])->name('track-violation');
    
    // Results
    Route::get('result/{attempt}', [EntryTestController::class, 'result'])->name('result');
});