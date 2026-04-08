<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminController::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('aeds', \App\Http\Controllers\AedController::class)->except(['destroy']);
    Route::put('aeds/{aed}/archive', [\App\Http\Controllers\AedController::class, 'archive'])->name('aeds.archive');
    Route::delete('aeds/{aed}', [\App\Http\Controllers\AedController::class, 'destroy'])->name('aeds.destroy');
});

require __DIR__.'/auth.php';
