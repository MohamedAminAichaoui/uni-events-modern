<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\ClubMembershipController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Manage;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// ---- Public ----
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

// ---- Authentication ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---- Any logged-in user ----
Route::middleware('auth')->group(function () {
    Route::get('/my-registrations', [RegistrationController::class, 'index'])->name('registrations.index');
    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::delete('/events/{event}/register', [RegistrationController::class, 'destroy'])->name('registrations.destroy');

    Route::post('/clubs/{club}/join', [ClubMembershipController::class, 'store'])->name('clubs.join');
    Route::delete('/clubs/{club}/join', [ClubMembershipController::class, 'destroy'])->name('clubs.leave');
});

// ---- Admin + club managers: event management ----
Route::middleware(['auth', 'role:admin,club_manager'])->prefix('manage')->name('manage.')->group(function () {
    Route::resource('events', Manage\EventController::class);
});

// ---- Admin only ----
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('analytics/events', [DashboardController::class, 'events'])->name('analytics.events');
    Route::get('analytics/clubs', [DashboardController::class, 'clubs'])->name('analytics.clubs');
    Route::resource('clubs', Admin\ClubController::class)->except('show');
    Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::put('users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
});
