<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\MaintenanceRecord;
use App\Models\User;

class MaintenanceRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array(
            $user->role,
            [
                UserRole::Admin,
                UserRole::Staff,
            ],
            true
        );
    }

    public function view(
        User $user,
        MaintenanceRecord $maintenance
    ): bool {
        return in_array(
            $user->role,
            [
                UserRole::Admin,
                UserRole::Staff,
            ],
            true
        );
    }

    public function create(User $user): bool
    {
        return in_array(
            $user->role,
            [
                UserRole::Admin,
                UserRole::Staff,
            ],
            true
        );
    }

    public function update(
        User $user,
        MaintenanceRecord $maintenance
    ): bool {
        return in_array(
            $user->role,
            [
                UserRole::Admin,
                UserRole::Staff,
            ],
            true
        );
    }

    public function delete(
        User $user,
        MaintenanceRecord $maintenance
    ): bool {
        return $user->role === UserRole::Admin;
    }
}