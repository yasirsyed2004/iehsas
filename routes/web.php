<?php
// File: routes/web.php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Homepage Route - Redirect to Admin Login
|--------------------------------------------------------------------------
| Since there's no public homepage, redirect to admin login page
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // If logged in as admin guard, go to admin dashboard
    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    // If logged in as web user, redirect to staff dashboard
    if (auth()->check()) {
        return redirect()->route('staff.dashboard');
    }
    // Not logged in, go to user login
    return redirect()->route('login');
})->name('home');

Route::get('/home', function () {
    return redirect('/');
});

// Include modular routes
require __DIR__.'/modules/admin.php';
require __DIR__.'/modules/entry-test.php';
require __DIR__.'/modules/enrollment.php';     // Phase 2 Enrollment Routes
require __DIR__.'/modules/courses.php';
require __DIR__.'/modules/staff.php';
require __DIR__.'/auth.php';