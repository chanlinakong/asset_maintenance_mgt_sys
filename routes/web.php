<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MaintenanceRecordController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [
    DashboardController::class,
    'index',
])->name('dashboard');

Route::resource('vehicles', VehicleController::class);

Route::resource(
    'maintenance',
    MaintenanceRecordController::class
)->parameters([
    'maintenance' => 'maintenance',
]);