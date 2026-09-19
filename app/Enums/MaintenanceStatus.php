<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function canTransitionTo(
        self $newStatus
    ): bool {
        return match ($this) {

            self::Pending => in_array(
                $newStatus,
                [
                    self::InProgress,
                    self::Cancelled,
                ],
                true
            ),

            self::InProgress => in_array(
                $newStatus,
                [
                    self::Completed,
                    self::Cancelled,
                ],
                true
            ),

            self::Completed => false,

            self::Cancelled => false,
        };
    }

    public function allowedNextStatuses(): array
    {
        return match ($this) {
            self::Pending => [
                self::Pending,
                self::InProgress,
                self::Cancelled,
            ],

            self::InProgress => [
                self::InProgress,
                self::Completed,
                self::Cancelled,
            ],

            self::Completed => [
                self::Completed,
            ],

            self::Cancelled => [
                self::Cancelled,
            ],
        };
    }
}