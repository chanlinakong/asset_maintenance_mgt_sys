<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property Carbon|null $last_service_date
 * @property Carbon|null $next_due_date
 */
class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'title',
        'description',
        'interval_days',
        'interval_kilometers',
        'last_service_date',
        'next_due_date',
        'last_service_kilometers',
        'next_due_kilometers',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'last_service_date' => 'date',
            'next_due_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(
            MaintenanceRecord::class
        );
    }

    public function isOverdue(): bool
    {
        if (!$this->is_active || !$this->next_due_date) {
            return false;
        }

        return $this->next_due_date->isPast();
    }

    public function isDueSoon(int $days = 30): bool
    {
        if (!$this->is_active || !$this->next_due_date) {
            return false;
        }

        return $this->next_due_date->between(
            today(),
            today()->addDays($days)
        );
    }

    public function calculateNextDueKilometers(
        ?int $serviceKilometers = null
    ): ?int {
        if (
            !$this->interval_kilometers
            || $serviceKilometers === null
        ) {
            return null;
        }

        return $serviceKilometers
            + $this->interval_kilometers;
    }

    public function markServiced(
        ?Carbon $serviceDate = null,
        ?int $kilometers = null
    ): void {
        $serviceDate ??= today();

        $this->last_service_date = $serviceDate;

        if ($this->interval_days) {
            $this->next_due_date = $serviceDate
                ->copy()
                ->addDays($this->interval_days);
        } else {
            $this->next_due_date = null;
        }

        if (
            $this->interval_kilometers
            && $kilometers !== null
        ) {
            $this->last_service_kilometers = $kilometers;

            $this->next_due_kilometers =
                $kilometers + $this->interval_kilometers;
        } else {
            $this->last_service_kilometers = $kilometers;

            if (!$this->interval_kilometers) {
                $this->next_due_kilometers = null;
            }
        }

        $this->save();
    }

    public function isKilometersDue(
        ?int $currentKilometers = null
    ): bool {
        if (
            !$this->is_active
            || !$this->next_due_kilometers
            || $currentKilometers === null
        ) {
            return false;
        }

        return $currentKilometers >= $this->next_due_kilometers;
    }

    public function dueStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->isOverdue()) {
            return 'overdue';
        }

        if ($this->isDueSoon()) {
            return 'due_soon';
        }

        return 'up_to_date';
    }

    public function dueStatusLabel(): string
    {
        return match ($this->dueStatus()) {
            'inactive' => 'Inactive',
            'overdue' => 'Overdue',
            'due_soon' => 'Due Soon',
            'up_to_date' => 'Up to Date',
        };
    }

    public function dueStatusClasses(): string
    {
        return match ($this->dueStatus()) {
            'inactive' =>
                'bg-gray-100 text-gray-600',

            'overdue' =>
                'bg-red-100 text-red-700',

            'due_soon' =>
                'bg-yellow-100 text-yellow-700',

            'up_to_date' =>
                'bg-green-100 text-green-700',
        };
    }
}