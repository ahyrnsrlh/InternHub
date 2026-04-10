<?php

use App\Http\Controllers\InternHubController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('internhub')->name('internhub.')->group(function () {
    Route::get('/login', fn () => view('pages.login'))->name('login');
    Route::get('/register', fn () => view('pages.register'))->name('register');

    Route::get('/dashboard', [InternHubController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [InternHubController::class, 'attendance'])->name('attendance');
    Route::get('/daily-logbook', [InternHubController::class, 'dailyLogbook'])->name('daily-logbook');
    Route::get('/registration', fn () => view('pages.internship-registration'))->name('registration');
    Route::get('/admin-control-center', [InternHubController::class, 'adminControlCenter'])->name('admin-center');
    Route::get('/mentor-review-panel', [InternHubController::class, 'mentorReviewPanel'])->name('mentor-review');
    Route::get('/monthly-logbook-summary', fn () => view('pages.monthly-logbook-summary'))->name('monthly-summary');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
