<?php

use App\Http\Controllers\AnalyticController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Profiler\Profile;

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
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('edit-account-info', [ProfileController::class, 'accountInfo'])->name('account.info');
    Route::post('edit-account-info', [ProfileController::class, 'accountInfoStore'])->name('account.info.store');
    Route::post('change-password', [ProfileController::class, 'changePasswordStore'])->name('account.password.store');

    Route::resource('roles', RoleController::class)->middleware(['role:superadmin', 'permission:role-list|role-create|role-edit|role-delete']);
    Route::resource('user', UserController::class)->middleware(['role:superadmin', 'permission:user-list|user-create|user-edit|user-delete']);
    Route::resource('request', RequestController::class)->middleware(['role:superadmin|admin', 'permission:request-list|request-create|request-edit|request-acceptance|request-delete']);
    Route::post('/request/acceptance', [RequestController::class, 'acceptance'])->middleware(['role:superadmin|admin', 'permission:request-acceptance'])->name('request.acceptance');

    Route::get('scanner', [ScannerController::class, 'index'])->name('scanner.index');

    Route::get('analytic', [AnalyticController::class, 'index'])->name('analytic');
    Route::get('request-export/{year}/{month}', [AnalyticController::class, 'export'])->name('request.export');
});

require __DIR__.'/auth.php';
