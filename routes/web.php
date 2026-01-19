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
    return redirect()->route('admin.login');
})->name('home');

Route::get('/home', function () {
    return redirect()->route('admin.login');
});

// Include modular routes
require __DIR__.'/modules/admin.php';
require __DIR__.'/modules/entry-test.php';
require __DIR__.'/modules/enrollment.php';     // Phase 2 Enrollment Routes
require __DIR__.'/modules/courses.php';
require __DIR__.'/auth.php';