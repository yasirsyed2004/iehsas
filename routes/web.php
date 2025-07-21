<?php
// File: routes/web.php

use Illuminate\Support\Facades\Route;

// Homepage Route
Route::get('/', function () {
    return view('homepage');
})->name('home');

// Include modular routes
require __DIR__.'/modules/admin.php';
require __DIR__.'/modules/entry-test.php';
require __DIR__.'/modules/courses.php';
require __DIR__.'/auth.php';