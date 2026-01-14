<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Get notes for the logged-in user
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $notes = $user->notes()->latest()->get();
    return view('dashboard', compact('notes'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notes resource routes
    Route::resource('notes', NoteController::class);
});
// Email Routes
Route::middleware(['auth'])->group(function () {
    // Email settings
    Route::get('/email/settings', [EmailController::class, 'settings'])->name('email.settings');
    Route::post('/email/settings', [EmailController::class, 'updateSettings'])->name('email.settings.update');
    Route::post('/email/unsubscribe/{token}', [EmailController::class, 'unsubscribe'])->name('email.unsubscribe');
    Route::get('/email/subscribe/{token}', [EmailController::class, 'subscribe'])->name('email.subscribe');

    // Email preview
    Route::get('/email/preview/{template}', [EmailController::class, 'preview'])->name('email.preview');

    // Email analytics
    Route::get('/email/analytics', [EmailController::class, 'analytics'])->name('email.analytics');

    // Invitations
    Route::get('/invitations/accept/{token}', [InvitationController::class, 'showAccept'])->name('invitations.show');
    Route::post('/invitations/accept/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/decline/{token}', [InvitationController::class, 'decline'])->name('invitations.decline');
});

// Public email tracking routes (no auth required)
Route::get('/email/track/open/{emailId}', [EmailController::class, 'trackOpen'])->name('email.track.open');
Route::get('/email/track/click/{emailId}', [EmailController::class, 'trackClick'])->name('email.track.click');

// Email verification
Route::get('/email/verify/{token}', [EmailController::class, 'verify'])->name('email.verify');

require __DIR__.'/auth.php';
