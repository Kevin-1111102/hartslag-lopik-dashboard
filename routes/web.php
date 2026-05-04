<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AedController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// AED routes
Route::resource('aeds', AedController::class)->only(['index', 'show', 'create', 'store', 'destroy'])->middleware(['auth', 'verified']);
Route::patch('/aeds/{aed}/archive', [AedController::class, 'archive'])->name('aeds.archive')->middleware(['auth', 'verified', 'admin']);
Route::patch('/aeds/{aed}/unarchive', [AedController::class, 'unarchive'])->name('aeds.unarchive')->middleware(['auth', 'verified', 'admin']);
Route::get('/aeds/archief/overzicht', [AedController::class, 'archief'])->name('aeds.archief')->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminController::class, 'index'])->name('users');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store']);
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [AdminController::class, 'update'])->name('users.update');
});

require __DIR__.'/auth.php';
