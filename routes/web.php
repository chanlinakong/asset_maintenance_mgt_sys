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
use App\Http\Controllers\DefectTypeController;
use App\Http\Controllers\MaintenanceRepairWorkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\VehicleAttachmentController;

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

    // Defect Types
    Route::resource('defect-types', DefectTypeController::class)
        ->except(['destroy']);

    Route::patch(
        '/defect-types/{defectType}/toggle-active',
        [DefectTypeController::class, 'toggleActive']
    )->name('defect-types.toggle-active');

    Route::get(
        '/vehicles/{vehicle}/available-defects',
        [MaintenanceRecordController::class, 'availableDefects']
    )->name('vehicles.available-defects');

    Route::post(
        '/maintenance/{maintenance}/visits',
        [MaintenanceRecordController::class, 'storeVisit']
    )->name('maintenance.visits.store');

    Route::patch(
        '/maintenance/{maintenance}/visits/{visit}/complete',
        [MaintenanceRecordController::class, 'completeVisit']
    )->name('maintenance.visits.complete');
    Route::patch(
        '/maintenance/{maintenance}/visits/{visit}/defects/{recordDefect}/outcome',
        [MaintenanceRecordController::class, 'updateDefectOutcome']
    )->name('maintenance.visits.defects.outcome');
    Route::post(
        '/maintenance/{maintenance}/visits/{visit}/defects/{recordDefect}/repair-works',
        [MaintenanceRepairWorkController::class, 'store']
    )->name('maintenance.repair-works.store');

    Route::put(
        '/maintenance/{maintenance}/visits/{visit}/defects/{recordDefect}/repair-works/{repairWork}',
        [MaintenanceRepairWorkController::class, 'update']
    )->name('maintenance.repair-works.update');

    Route::delete(
        '/maintenance/{maintenance}/visits/{visit}/defects/{recordDefect}/repair-works/{repairWork}',
        [MaintenanceRepairWorkController::class, 'destroy']
    )->name('maintenance.repair-works.destroy');

    Route::post(
        '/maintenance/{maintenance}/visits/{visit}/defects/{recordDefect}/repair-works/{repairWork}/parts',
        [MaintenancePartController::class, 'store']
    )->name('maintenance.parts.store');

    Route::put(
        '/maintenance/{maintenance}/visits/{visit}/defects/{recordDefect}/repair-works/{repairWork}/parts/{maintenancePart}',
        [MaintenancePartController::class, 'update']
    )->name('maintenance.parts.update');

    Route::delete(
        '/maintenance/{maintenance}/visits/{visit}/defects/{recordDefect}/repair-works/{repairWork}/parts/{maintenancePart}',
        [MaintenancePartController::class, 'destroy']
    )->name('maintenance.parts.destroy');

    // Maintenance Visit Attachments

    Route::post(
        '/maintenance/{maintenance}/visits/{visit}/attachments',
        [MaintenanceAttachmentController::class, 'store']
    )->name('maintenance.visits.attachments.store');

    Route::delete(
        '/maintenance/{maintenance}/visits/{visit}/attachments/{attachment}',
        [MaintenanceAttachmentController::class, 'destroy']
    )->name('maintenance.visits.attachments.destroy');
});

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
    Route::post(
        '/vehicles/{vehicle}/attachments',
        [VehicleAttachmentController::class, 'store']
    )->name('vehicles.attachments.store');

    Route::delete(
        '/vehicles/{vehicle}/attachments/{attachment}',
        [VehicleAttachmentController::class, 'destroy']
    )->name('vehicles.attachments.destroy');

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
        [MaintenanceAttachmentController::class, 'destroyGeneral']
    )->name('maintenance.attachments.destroy');

    // Maintenance-Part

    // Legacy: parts directly under maintenance report
    Route::post(
        '/maintenance/{maintenance}/parts',
        [MaintenancePartController::class, 'store']
    )->name('maintenance.legacy-parts.store');

    Route::delete(
        '/maintenance/{maintenance}/parts/{maintenancePart}',
        [MaintenancePartController::class, 'destroy']
    )->name('maintenance.legacy-parts.destroy');

    // Parts Catalog — Admin only
    Route::resource('parts', PartController::class)
        ->except(['show', 'destroy']);

    Route::patch(
        '/parts/{part}/toggle-active',
        [PartController::class, 'toggleActive']
    )->name('parts.toggle-active');

});

require __DIR__ . '/auth.php';