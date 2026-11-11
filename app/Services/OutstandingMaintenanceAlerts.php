<?php

namespace App\Services;

use App\Models\MaintenanceSchedule;
use Illuminate\Support\Collection;

class OutstandingMaintenanceAlerts
{
    public function get(): Collection
    {
        return MaintenanceSchedule::query()
            ->with('vehicle')
            ->where('is_active', true)
            ->get()
            ->filter(function (MaintenanceSchedule $schedule) {
                // Preserve your existing rule: do not alert
                // when maintenance is already in progress.
                if ($schedule->activeMaintenance()) {
                    return false;
                }

                $status = $schedule->dueStatus(
                    $schedule->vehicle?->current_kilometers
                );

                return in_array(
                    $status,
                    ['due_soon', 'overdue'],
                    true
                );
            })
            ->sortBy(fn ($schedule) =>
                $schedule->dueStatus(
                    $schedule->vehicle?->current_kilometers
                ) === 'overdue' ? 0 : 1
            )
            ->values();
    }
}