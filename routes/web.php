<?php

use App\Http\Controllers\CaseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// All case pages & dashboard protected by 'auth'
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [CaseController::class, 'dashboard'])->name('dashboard');

    Route::resource('cases', CaseController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
