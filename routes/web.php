<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AedController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Models\Notification;

Route::get('/dashboard', function () {
    $recentUnread = null;

    if (auth()->check() && auth()->user()?->is_admin) {
        $recentUnread = Notification::query()
            ->where('gelezen', false)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();
    }

    return view('dashboard', compact('recentUnread'));
})->middleware(['auth', 'verified'])->name('dashboard');

// AED routes
Route::resource('aeds', AedController::class)
    ->middleware(['auth', 'verified']);

// Admin-only: aanmaken/verwijderen AED's
Route::resource('aeds', AedController::class)
    ->only(['create', 'store', 'destroy'])
    ->middleware(['auth', 'verified', 'admin']);

// Controle routes
Route::get('/aeds/{aed}/controle', [AedController::class, 'controle'])->name('aeds.controle.show')->middleware(['auth', 'verified']);
Route::post('/aeds/{aed}/controle', [AedController::class, 'controleStore'])->name('aeds.controle.store')->middleware(['auth', 'verified']);
Route::get('/aeds/{aed}/controles/history', [AedController::class, 'controleHistory'])->name('aeds.controle.history')->middleware(['auth', 'verified']);


Route::patch('/aeds/{aed}/archive', [AedController::class, 'archive'])->name('aeds.archive')->middleware(['auth', 'verified', 'admin']);
Route::patch('/aeds/{aed}/unarchive', [AedController::class, 'unarchive'])->name('aeds.unarchive')->middleware(['auth', 'verified', 'admin']);
Route::get('/aeds/{aed}/cooperation-agreement/view', [AedController::class, 'viewCooperationAgreement'])
    ->name('aeds.cooperation-agreement.view')
    ->middleware(['auth', 'verified']);
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

    // Notifications
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{notification}/read', [AdminController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/{notification}/unread', [AdminController::class, 'markNotificationUnread'])->name('notifications.unread');
});

require __DIR__.'/auth.php';
