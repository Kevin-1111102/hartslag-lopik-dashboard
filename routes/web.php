<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AedController;
use App\Http\Controllers\TwoFactorController;

use App\Models\Notification;

/*
|--------------------------------------------------------------------------
| Public route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {

    $unreadNotifications = Notification::query()
        ->where('gelezen', false)
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->get();

    $now = now();
    $warningDays = 60;

    $aedsActieVereist = \App\Models\Aed::query()
        ->where('status', '!=', 'archief')
        ->where(function ($q) use ($now, $warningDays) {
            $q->where(function ($q1) use ($now, $warningDays) {
                $q1->whereNotNull('batterij_vervaldatum')
                    ->where('batterij_vervaldatum', '<=', $now->copy()->addDays($warningDays));
            })
            ->orWhere(function ($q2) use ($now, $warningDays) {
                $q2->whereNotNull('elektroden_vervaldatum')
                    ->where('elektroden_vervaldatum', '<=', $now->copy()->addDays($warningDays));
            });
        })
        ->get();

    $aeds = \App\Models\Aed::query()
        ->where('status', '!=', 'archief');

    $batterijExpired = (clone $aeds)
        ->whereNotNull('batterij_vervaldatum')
        ->where('batterij_vervaldatum', '<', $now)
        ->get();

    $batterijWarning = (clone $aeds)
        ->whereNotNull('batterij_vervaldatum')
        ->where('batterij_vervaldatum', '>=', $now)
        ->where('batterij_vervaldatum', '<=', $now->copy()->addDays($warningDays))
        ->get();

    $batterijGoed = (clone $aeds)
        ->where(function ($q) use ($now, $warningDays) {
            $q->whereNull('batterij_vervaldatum')
              ->orWhere('batterij_vervaldatum', '>', $now->copy()->addDays($warningDays));
        })
        ->get();

    $elektrodenExpired = (clone $aeds)
        ->whereNotNull('elektroden_vervaldatum')
        ->where('elektroden_vervaldatum', '<', $now)
        ->get();

    $elektrodenWarning = (clone $aeds)
        ->whereNotNull('elektroden_vervaldatum')
        ->where('elektroden_vervaldatum', '>=', $now)
        ->where('elektroden_vervaldatum', '<=', $now->copy()->addDays($warningDays))
        ->get();

    $elektrodenGoed = (clone $aeds)
        ->where(function ($q) use ($now, $warningDays) {
            $q->whereNull('elektroden_vervaldatum')
              ->orWhere('elektroden_vervaldatum', '>', $now->copy()->addDays($warningDays));
        })
        ->get();

    $recentUnread = Notification::query()
        ->where('gelezen', false)
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->first();

    return view('dashboard', compact(
        'unreadNotifications',
        'aedsActieVereist',
        'recentUnread',
        'batterijExpired',
        'batterijWarning',
        'batterijGoed',
        'elektrodenExpired',
        'elektrodenWarning',
        'elektrodenGoed'
    ));

})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| AED routes
|--------------------------------------------------------------------------
*/
Route::get('/aeds/export', [AedController::class, 'exportAll'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('aeds.export');

Route::get('/aeds/archief/overzicht', [AedController::class, 'archief'])
    ->middleware(['auth', 'verified'])
    ->name('aeds.archief');

Route::get('/aeds/kaart', [AedController::class, 'map'])
    ->middleware(['auth', 'verified'])
    ->name('aeds.map');

Route::get('/aeds/kaart/locations', [AedController::class, 'mapLocations'])
    ->middleware(['auth', 'verified'])
    ->name('aeds.map.locations');

/*
|--------------------------------------------------------------------------
| AED resource routes
|--------------------------------------------------------------------------
*/
Route::resource('aeds', AedController::class)
    ->middleware(['auth', 'verified']);

// Admin-only create/store/destroy override
Route::resource('aeds', AedController::class)
    ->only(['create', 'store', 'destroy'])
    ->middleware(['auth', 'verified', 'admin']);

/*
|--------------------------------------------------------------------------
| Controle routes
|--------------------------------------------------------------------------
*/
Route::get('/aeds/{aed}/controle', [AedController::class, 'controle'])
    ->middleware(['auth', 'verified'])
    ->name('aeds.controle.show');

Route::post('/aeds/{aed}/controle', [AedController::class, 'controleStore'])
    ->middleware(['auth', 'verified'])
    ->name('aeds.controle.store');

Route::get('/aeds/{aed}/controles/history', [AedController::class, 'controleHistory'])
    ->middleware(['auth', 'verified'])
    ->name('aeds.controle.history');

Route::patch('/aeds/{aed}/archive', [AedController::class, 'archive'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('aeds.archive');

Route::patch('/aeds/{aed}/unarchive', [AedController::class, 'unarchive'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('aeds.unarchive');

Route::get('/aeds/{aed}/cooperation-agreement/view', [AedController::class, 'viewCooperationAgreement'])
    ->middleware(['auth', 'verified'])
    ->name('aeds.cooperation-agreement.view');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 2FA ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/confirm', [TwoFactorController::class, 'confirm'])->name('2fa.confirm');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    Route::get('/2fa/login', function () {
        return view('auth.2fa-login');
    })->name('2fa.login');

    Route::post('/2fa/login', [TwoFactorController::class, 'loginCheck']);
});

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/users', [AdminController::class, 'index'])->name('users');
        Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminController::class, 'store']);
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [AdminController::class, 'update'])->name('users.update');

        Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
        Route::post('/notifications/{notification}/read', [AdminController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/{notification}/unread', [AdminController::class, 'markNotificationUnread'])->name('notifications.unread');
    });

require __DIR__.'/auth.php';