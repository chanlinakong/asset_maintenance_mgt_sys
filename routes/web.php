<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaintenanceScheduleController;
use App\Http\Controllers\MaintenanceAttachmentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MaintenancePartController;

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

    Route::get('/reports', [
        ReportController::class,
        'index',
    ])->name('reports.index');

    Route::get('/reports/pdf', [
        ReportController::class,
        'pdf',
    ])->name('reports.pdf');

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
    )->parameters(['maintenance' => 'maintenance',]);

    Route::resource(
        'maintenance-schedules',
        MaintenanceScheduleController::class
    );

    Route::get(
        '/vehicles/{vehicle}/maintenance-schedules',
        [MaintenanceScheduleController::class, 'forVehicle']
    )->name('vehicles.maintenance-schedules');

    // Notification

    Route::get(
        '/notifications',
        [NotificationController::class, 'index']
    )->name('notifications.index');

    Route::get(
        '/notifications/{notification}/read',
        [NotificationController::class, 'read']
    )->name('notifications.read');

    Route::post(
        '/notifications/read-all',
        [NotificationController::class, 'readAll']
    )->name('notifications.read-all');

    // Maintenance-Attachment

    Route::post(
        '/maintenance/{maintenance}/attachments',
        [
            MaintenanceAttachmentController::class,
            'store',
        ]
    )->name(
            'maintenance.attachments.store'
        );

    Route::delete(
        '/maintenance/{maintenance}/attachments/{attachment}',
        [
            MaintenanceAttachmentController::class,
            'destroy',
        ]
    )->name('maintenance.attachments.destroy');

    // Maintenance-Part

    Route::post(
        '/maintenance/{maintenance}/parts',
        [
            MaintenancePartController::class,
            'store',
        ]
    )->name(
            'maintenance.parts.store'
        );

    Route::delete(
        '/maintenance/{maintenance}/parts/{maintenancePart}',
        [
            MaintenancePartController::class,
            'destroy',
        ]
    )->name(
            'maintenance.parts.destroy'
        );

});

require __DIR__ . '/auth.php';