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
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Settings\UserManagementController;
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

    Route::get('/reports/excel', [
        ReportController::class,
        'excel',
    ])->name('reports.excel');

});

use App\Http\Controllers\ProfileController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::patch('/profile/avatar', [
        ProfileController::class,
        'updateAvatar',
    ])->name('profile.avatar.update');
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

    Route::prefix('settings')
        ->name('settings.')
        ->group(function () {

            Route::get('/', [SettingsController::class, 'index'])
                ->name('index');

            Route::get('/users', [UserManagementController::class, 'index'])
                ->name('users.index');

            Route::get('/users/create', [UserManagementController::class, 'create'])
                ->name('users.create');

            Route::post('/users', [UserManagementController::class, 'store'])
                ->name('users.store');

            Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])
                ->name('users.update-role');

            Route::patch('/users/{user}/status', [UserManagementController::class, 'toggleActive'])
                ->name('users.toggle-active');
        });
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

    Route::patch(
        '/maintenance-schedules/{maintenanceSchedule}/toggle-active',
        [MaintenanceScheduleController::class, 'toggleActive']
    )->name('maintenance-schedules.toggle-active');

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