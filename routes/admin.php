<?php

use Illuminate\Support\Facades\Route;
// Controllers will be created step by step

// Admin Authentication Routes (Public)
Route::prefix('admin')->name('admin.')->group(function () {
    
    Route::get('test', function () {
        return 'Admin routes working!';
    });
});