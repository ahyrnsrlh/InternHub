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
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\LocationTrackingController;
use App\Http\Controllers\User\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::redirect('/internhub/login', '/login');

Route::get('/dashboard', function () {
    return redirect()->route('internhub.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('internhub')->name('internhub.')->middleware('guest')->group(function () {
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
            return redirect()->route('user.dashboard.index');
        }

        abort(403);
    })->middleware('face.registered')->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminPageController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/charts/attendance', [AdminPageController::class, 'getAdminAttendanceStats'])->name('dashboard.charts.attendance');
        Route::get('/dashboard/charts/validation', [AdminPageController::class, 'getAdminValidationStats'])->name('dashboard.charts.validation');
        Route::get('/dashboard/charts/trend', [AdminPageController::class, 'getAdminTrendStats'])->name('dashboard.charts.trend');
        Route::get('/dashboard/charts/top-interns', [AdminPageController::class, 'getTopInterns'])->name('dashboard.charts.top-interns');
        Route::get('/interns', [AdminPageController::class, 'interns'])->name('interns');
        Route::post('/interns', [AdminPageController::class, 'storeIntern'])->name('interns.store');
        Route::get('/interns/{intern}', [AdminPageController::class, 'internDetail'])->name('intern-detail');
        Route::put('/interns/{internUser}', [AdminPageController::class, 'updateIntern'])->name('interns.update');
        Route::delete('/interns/{internUser}', [AdminPageController::class, 'destroyIntern'])->name('interns.destroy');
        Route::get('/attendance', [AdminPageController::class, 'attendance'])->name('attendance');
        Route::get('/locations', [AdminPageController::class, 'locations'])->name('locations');
        Route::post('/locations', [AdminPageController::class, 'storeLocation'])->name('locations.store');
        Route::put('/locations/{location}', [AdminPageController::class, 'updateLocation'])->name('locations.update');
        Route::delete('/locations/{location}', [AdminPageController::class, 'destroyLocation'])->name('locations.destroy');
        Route::get('/reports', [AdminPageController::class, 'reports'])->name('reports');
    });

    Route::name('intern.')->middleware('role:intern,user')->group(function () {
        Route::view('/attendance', 'pages.user.attendance')->middleware('face.registered')->name('attendance');
        Route::view('/locations', 'pages.user.locations')->name('locations');
        Route::view('/map', 'pages.user.map')->name('map');
        Route::get('/logbook', [LogbookController::class, 'index'])->middleware('face.registered')->name('logbook');
        Route::view('/reports', 'pages.user.reports')->middleware('face.registered')->name('reports');
        Route::view('/profile', 'pages.user.profile')->name('profile');
    });

    Route::redirect('/daily-logbook', '/internhub/logbook')->name('daily-logbook');
    Route::redirect('/registration', '/internhub/locations')->name('registration');
    Route::redirect('/admin-control-center', '/internhub/admin/dashboard')->name('admin-center');
    Route::redirect('/mentor-review-panel', '/internhub/reports')->name('mentor-review');

    Route::redirect('/user/dashboard', '/internhub/dashboard');
    Route::redirect('/user/attendance', '/internhub/attendance');
    Route::redirect('/user/locations', '/internhub/locations');
    Route::redirect('/user/map', '/internhub/map');
    Route::redirect('/user/logbook', '/internhub/logbook');
    Route::redirect('/user/reports', '/internhub/reports');
    Route::redirect('/user/profile', '/internhub/profile');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('user')->name('user.')->middleware(['auth', 'verified', 'role:intern,user'])->group(function () {
    Route::post('/profile/face', [UserProfileController::class, 'storeFace'])->name('profile.face.store');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/location-tracking', [LocationTrackingController::class, 'store'])->name('location-tracking.store');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->middleware('face.registered')->name('attendance.check-in');
    Route::patch('/attendance/check-out', [AttendanceController::class, 'checkOut'])->middleware('face.registered')->name('attendance.check-out');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->middleware('face.registered')->name('reports.export.pdf');
    Route::get('/logbook/export/pdf', [LogbookController::class, 'exportPdf'])->middleware('face.registered')->name('logbook.export.pdf');

    Route::resource('dashboard', UserController::class)
        ->except(['show'])
        ->middleware('face.registered');
    Route::get('/dashboard/charts/attendance-trend', [UserController::class, 'getUserAttendanceTrend'])->middleware('face.registered')->name('dashboard.charts.attendance-trend');
    Route::get('/dashboard/charts/validation', [UserController::class, 'getUserValidationStats'])->middleware('face.registered')->name('dashboard.charts.validation');
    Route::get('/dashboard/charts/activity', [UserController::class, 'getUserActivityStats'])->middleware('face.registered')->name('dashboard.charts.activity');
    Route::resource('attendance', AttendanceController::class)
        ->except(['show'])
        ->middleware('face.registered');
    Route::resource('locations', LocationController::class)
        ->except(['show']);
    Route::resource('logbook', LogbookController::class)
        ->except(['show'])
        ->middleware('face.registered');
    Route::resource('reports', ReportController::class)
        ->except(['show'])
        ->middleware('face.registered');
    Route::resource('profile', UserProfileController::class)
        ->except(['show']);

    Route::get('/map', [LocationController::class, 'map'])->name('map.index');
});

require __DIR__.'/auth.php';
