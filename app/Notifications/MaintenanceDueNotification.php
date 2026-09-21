<?php

namespace App\Notifications;

use App\Models\MaintenanceSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MaintenanceDueNotification extends Notification
{
    use Queueable;

    public function __construct(
        public MaintenanceSchedule $schedule,
        public string $reason = 'overdue',
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(
        object $notifiable
    ): array {
        return [
            'schedule_id' => $this->schedule->id,

            'vehicle_id' => $this->schedule->vehicle_id,

            'vehicle_code' =>
                $this->schedule->vehicle->vehicle_code,

            'vehicle_name' =>
                $this->schedule->vehicle->name,

            'title' =>
                $this->schedule->title,

            'reason' => $this->reason,

            'message' =>
                $this->buildMessage(),
        ];
    }

    private function buildMessage(): string
    {
        return match ($this->reason) {
            'overdue' =>
                "{$this->schedule->vehicle->vehicle_code} - "
                . "{$this->schedule->title} is overdue.",

            'due_soon' =>
                "{$this->schedule->vehicle->vehicle_code} - "
                . "{$this->schedule->title} is due soon.",

            'kilometers' =>
                "{$this->schedule->vehicle->vehicle_code} - "
                . "{$this->schedule->title} is due by mileage.",

            default =>
                "{$this->schedule->vehicle->vehicle_code} - "
                . "{$this->schedule->title} requires attention.",
        };
    }
}