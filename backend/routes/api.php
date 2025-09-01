<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\MedicationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/health', [HealthCheckController::class, 'index']);

// Public medication search endpoint
Route::get('/medications', [MedicationController::class, 'index']);

// Manager-only medication management endpoints
Route::middleware(['auth:sanctum', 'role:manager'])->group(function () {
    Route::post('/medications', [MedicationController::class, 'store']);
    Route::put('/medications/{id}', [MedicationController::class, 'update']);
    Route::delete('/medications/{id}', [MedicationController::class, 'destroy']);
    Route::get('/medications/low-stock', [MedicationController::class, 'lowStock']);
});
