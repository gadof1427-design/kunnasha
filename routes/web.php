<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QuoteController::class, 'index'])
    ->name('home');

Route::post('/quotes', [QuoteController::class, 'store'])
    ->middleware('auth')
    ->name('quotes.store');

// Permalink for a single quote (share links, notifications, link previews).
Route::get('/quotes/{quote}', [QuoteController::class, 'show'])
    ->whereNumber('quote')
    ->name('quotes.show');

Route::put('/quotes/{quote}', [QuoteController::class, 'update'])
    ->middleware('auth')
    ->name('quotes.update');

Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])
    ->middleware('auth')
    ->name('quotes.destroy');

Route::get('/explore', [QuoteController::class, 'explore'])
    ->name('explore');

// Public page of a member, by username (linked from "أُضيف بواسطة" on every card).
Route::get('/u/{user:username}', [UserController::class, 'show'])
    ->where('user', '[A-Za-z0-9_]+')
    ->name('users.show');

Route::get('/signup', function () {
    return view('Authentication.SignUp', ['page' => 'Sign Up']);
})->name('signup');

Route::post('/signup', [AuthController::class, 'register'])
    ->middleware('throttle:10,1')
    ->name('register');

Route::get('/login', function () {
    return view('Authentication.LogIn', ['page' => 'Log In']);
})->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('login.submit');

Route::get('/profile', [AuthController::class, 'profile'])
    ->middleware('auth')
    ->name('profile');

Route::put('/profile', [AuthController::class, 'updateProfile'])
    ->middleware('auth')
    ->name('profile.update');

Route::delete('/profile/photo', [AuthController::class, 'destroyProfilePhoto'])
    ->middleware('auth')
    ->name('profile.photo.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/{notification}/open', [NotificationController::class, 'open'])
        ->name('notifications.open');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.readAll');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/language/{locale}', function ($locale) {
    abort_unless(in_array($locale, ['ar', 'en']), 404);

    session(['locale' => $locale]);

    return redirect()->back();
})->name('language.switch');
