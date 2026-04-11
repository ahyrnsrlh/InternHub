<?php

use App\Models\User;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\LogbookController;
use App\Http\Controllers\User\LocationController;
use App\Http\Controllers\User\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('internhub.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('internhub')->name('internhub.')->middleware('guest')->group(function () {
    Route::view('/login', 'pages.user.auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
});

Route::prefix('internhub')->name('internhub.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = request()->user();

        if ($user?->hasRole(User::ROLE_ADMIN)) {
            return redirect()->route('internhub.admin.dashboard');
        }

        if ($user?->hasRole(User::ROLE_INTERN, User::ROLE_USER)) {
            return view('pages.user.dashboard');
        }

        abort(403);
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminPageController::class, 'dashboard'])->name('dashboard');
        Route::get('/interns', [AdminPageController::class, 'interns'])->name('interns');
        Route::get('/interns/{intern}', [AdminPageController::class, 'internDetail'])->name('intern-detail');
        Route::get('/attendance', [AdminPageController::class, 'attendance'])->name('attendance');
        Route::get('/locations', [AdminPageController::class, 'locations'])->name('locations');
        Route::post('/locations', [AdminPageController::class, 'storeLocation'])->name('locations.store');
        Route::put('/locations/{location}', [AdminPageController::class, 'updateLocation'])->name('locations.update');
        Route::delete('/locations/{location}', [AdminPageController::class, 'destroyLocation'])->name('locations.destroy');
        Route::get('/reports', [AdminPageController::class, 'reports'])->name('reports');
    });

    Route::name('intern.')->middleware('role:intern,user')->group(function () {
        Route::view('/attendance', 'pages.user.attendance')->name('attendance');
        Route::view('/locations', 'pages.user.locations')->name('locations');
        Route::view('/map', 'pages.user.map')->name('map');
        Route::view('/logbook', 'pages.user.logbook')->name('logbook');
        Route::view('/reports', 'pages.user.reports')->name('reports');
        Route::view('/recap', 'pages.user.recap')->name('recap');
        Route::view('/profile', 'pages.user.profile')->name('profile');
    });

    Route::redirect('/daily-logbook', '/internhub/logbook')->name('daily-logbook');
    Route::redirect('/registration', '/internhub/locations')->name('registration');
    Route::redirect('/admin-control-center', '/internhub/admin/dashboard')->name('admin-center');
    Route::redirect('/mentor-review-panel', '/internhub/reports')->name('mentor-review');
    Route::redirect('/monthly-logbook-summary', '/internhub/recap')->name('monthly-summary');

    Route::redirect('/user/dashboard', '/internhub/dashboard');
    Route::redirect('/user/attendance', '/internhub/attendance');
    Route::redirect('/user/locations', '/internhub/locations');
    Route::redirect('/user/map', '/internhub/map');
    Route::redirect('/user/logbook', '/internhub/logbook');
    Route::redirect('/user/reports', '/internhub/reports');
    Route::redirect('/user/recap', '/internhub/recap');
    Route::redirect('/user/profile', '/internhub/profile');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('user')->name('user.')->middleware(['auth', 'verified', 'role:intern,user'])->group(function () {
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::patch('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::resources([
        'dashboard' => UserController::class,
        'attendance' => AttendanceController::class,
        'locations' => LocationController::class,
        'logbook' => LogbookController::class,
        'reports' => ReportController::class,
        'profile' => UserProfileController::class,
    ], ['except' => ['show']]);

    Route::get('/map', [LocationController::class, 'map'])->name('map.index');
    Route::get('/recap', [ReportController::class, 'recap'])->name('recap.index');
});

require __DIR__.'/auth.php';
