<?php

use App\Models\UserReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\ProfileController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\PasswordResetLinkController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;


Route::get('/user-reports', function () {
    $expirationMinutes = 60;

    $activeReports = UserReport::where('created_at', '>=', now()->subMinutes($expirationMinutes))->get();

    return response()->json($activeReports);
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/reports', [AdminController::class, 'reports']);
    Route::post('/admin/reports/{id}/approve', [AdminController::class, 'approve']);
    Route::post('/admin/reports/{id}/clear', [AdminController::class, 'clear']);
    Route::delete('/admin/reports/{id}', [AdminController::class, 'destroy']);
    Route::get('/admin/users/create', [AdminUserController::class, 'create']);
    Route::post('/admin/users/store', [AdminUserController::class, 'store']);
});

Route::get('/admin', function () {
    return redirect('/admin/dashboard');
});

Route::get('/', [MapController::class, 'index']);
Route::get('/api/flood-data', [MapController::class, 'floodData']); // For JS AJAX
Route::middleware(['auth'])->group(function () {
    Route::get('/report', [UserReportController::class, 'create']);
    Route::post('/report', [UserReportController::class, 'store']);
});
Route::get('/api/sensors', [SensorDataController::class, 'index']);


Route::get('/redirect-after-login', function () {
    if (Auth::check() && Auth::user()->is_admin) {
        return redirect('/admin/dashboard');
    }

    return redirect('/'); // Normal user
});

Route::get('/dashboard', function () {
    // dummy dashboard. dont remove. if removed things will break
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

Route::get('/statistics', [StatisticsController::class, 'index']);

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
});

Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');


Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest']);

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware(['guest'])
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest']);

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.update');
