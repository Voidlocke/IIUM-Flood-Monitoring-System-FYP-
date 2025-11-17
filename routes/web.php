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
Route::get('/report', [UserReportController::class, 'create']);
Route::post('/report', [UserReportController::class, 'store']);
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
