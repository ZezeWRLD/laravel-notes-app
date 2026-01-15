<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
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

// Preview email - exactly like tutorial
Route::get('/preview-note-email', function () {
    $note = \App\Models\Note::first() ?? new \App\Models\Note([
        'title' => 'Sample Note',
        'content' => 'This is a sample note for preview.',
    ]);

    return new \App\Mail\NoteNotification($note, 'created');
});

Route::get('/search', SearchController::class)->name('search');

// Tags with route model binding
Route::get('/tags/{tag:slug}', TagController::class)->name('tags.show');

require __DIR__.'/auth.php';
