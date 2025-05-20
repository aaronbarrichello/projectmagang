<?php

use App\Http\Controllers\AnalyticController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/visit-request', function () {
    return Inertia::render('FormVisit');
})->name('form.visit');

Route::post('/visit-request', [RequestController::class, 'formVisitStore'])->name('form.visit.store');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('edit-account-info', [ProfileController::class, 'accountInfo'])->name('account.info');
    Route::post('edit-account-info', [ProfileController::class, 'accountInfoStore'])->name('account.info.store');
    Route::post('change-password', [ProfileController::class, 'changePasswordStore'])->name('account.password.store');
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');

    Route::prefix('api')->group(function () {
        Route::get('/scan/verify/{uuid}', [ScanController::class, 'verify'])->name('scan.verify');
        Route::post('/scan/check-in', [ScanController::class, 'checkIn'])->name('scan.check-in');
    });

    // Role & User management
    Route::resource('roles', RoleController::class)->middleware(['role:superadmin', 'permission:role-list|role-create|role-edit|role-delete']);
    Route::resource('user', UserController::class)->middleware(['role:superadmin', 'permission:user-list|user-create|user-edit|user-delete']);

    // Request management
    Route::resource('request', RequestController::class)->middleware(['role:superadmin|admin', 'permission:request-list|request-create|request-edit|request-acceptance|request-delete']);

    // Request acceptance
    Route::post('/request/{id}/{action}', [RequestController::class, 'acceptance'])
        ->middleware(['role:superadmin|admin', 'permission:request-acceptance'])
        ->name('request.acceptance');

    // Visitor KTP image display route (aman)
    Route::get('/admin/visitor/ktp/{id}', [RequestController::class, 'showKtp'])
        ->name('admin.visitor.ktp')
        ->middleware(['auth', 'role:superadmin|admin']);


    // User reject
    Route::post('/users/{id}/reject', [UserController::class, 'reject'])->name('user.reject');

    // Scanner and analytics
    Route::get('scanner', [ScanController::class, 'index'])->name('scanner.index');
    Route::get('analytic', [AnalyticController::class, 'index'])->name('analytic');
    Route::get('request-export/{year}/{month}', [AnalyticController::class, 'export'])->name('request.export');
});

require __DIR__.'/auth.php';