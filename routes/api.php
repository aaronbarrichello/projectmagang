<?php

use App\Http\Controllers\ScannerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/check-qrcode', [ScannerController::class, 'checkQrCode']);
