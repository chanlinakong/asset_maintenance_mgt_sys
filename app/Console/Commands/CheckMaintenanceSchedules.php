<?php

namespace App\Console\Commands;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use App\Notifications\MaintenanceDueNotification;
use Illuminate\Console\Command;

class CheckMaintenanceSchedules extends Command
{
    protected $signature =
        'maintenance:check-schedules';

    protected $description =
        'Check maintenance schedules and notify users about due maintenance';

    public function handle(): int
    {
        $schedules = MaintenanceSchedule::query()
            ->with([
                'vehicle',
                'maintenanceRecords',
            ])
            ->where('is_active', true)
            ->get();

        $users = User::query()
            ->whereIn('role', [
                'admin',
                'staff',
            ])
            ->get();

        $notified = 0;

        foreach ($schedules as $schedule) {

            if ($schedule->activeMaintenance()) {
                continue;
            }

            $currentKilometers =
                $schedule->vehicle
                    ->current_kilometers;

            if (
                $schedule->isDueByKilometers(
                    $currentKilometers
                )
            ) {

                $this->notifyUsers(
                    $users,
                    $schedule,
                    'kilometers'
                );

                $notified++;

                continue;
            }

            if ($schedule->isDueByDate()) {

                $this->notifyUsers(
                    $users,
                    $schedule,
                    'overdue'
                );

                $notified++;

                continue;
            }

            if ($schedule->isDueSoon()) {

                $this->notifyUsers(
                    $users,
                    $schedule,
                    'due_soon'
                );

                $notified++;
            }
        }

        $this->info(
            "Checked schedules. {$notified} require attention."
        );

        return self::SUCCESS;
    }

    //Also prevent duplicate notification in a day    
    private function notifyUsers(
        $users,
        MaintenanceSchedule $schedule,
        string $reason
    ): void {

        foreach ($users as $user) {

            $alreadyNotified =
                $user->notifications()
                    ->where(
                        'type',
                        MaintenanceDueNotification::class
                    )
                    ->whereDate(
                        'created_at',
                        today()
                    )
                    ->where(
                        'data->schedule_id',
                        $schedule->id
                    )
                    ->where(
                        'data->reason',
                        $reason
                    )
                    ->exists();

            if ($alreadyNotified) {
                continue;
            }

            $user->notify(
                new MaintenanceDueNotification(
                    $schedule,
                    $reason
                )
            );
        }
    }
}