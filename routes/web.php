<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::middleware(['auth', 'role:admin,staff'])->group(function () {

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');

});

use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::resource('vehicles', VehicleController::class)
        ->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy',
        ]);

});

Route::middleware(['auth', 'role:admin,staff'])->group(function () {

    Route::resource('vehicles', VehicleController::class)
        ->only([
            'index',
            'show',
        ]);

    Route::resource(
        'maintenance',
        MaintenanceRecordController::class
    )->parameters([
        'maintenance' => 'maintenance',
    ]);

});

require __DIR__.'/auth.php';