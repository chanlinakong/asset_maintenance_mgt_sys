<?php

namespace App\Policies;

use App\Models\MaintenanceSchedule;
use App\Models\User;

class MaintenanceSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array(
            $user->role->value,
            ['admin', 'staff'],
            true
        );
    }

    public function view(
        User $user,
        MaintenanceSchedule $maintenanceSchedule
    ): bool {
        return in_array(
            $user->role->value,
            ['admin', 'staff'],
            true
        );
    }

    public function create(User $user): bool
    {
        return in_array(
            $user->role->value,
            ['admin', 'staff'],
            true
        );
    }

    public function update(
        User $user,
        MaintenanceSchedule $maintenanceSchedule
    ): bool {
        //return $user->role->value === 'admin';
        return in_array(
            $user->role->value,
            ['admin', 'staff'],
            true
        );
    }

    public function delete(
        User $user,
        MaintenanceSchedule $maintenanceSchedule
    ): bool {
        //return $user->role->value === 'admin';
        return in_array(
            $user->role->value,
            ['admin', 'staff'],
            true
        );
    }
}