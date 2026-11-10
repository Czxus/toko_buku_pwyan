<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

/**
 * Keputusan administratif dipusatkan di policy, bukan string role yang tersebar.
 * Pada SRS ini pengelola platform = users.role 'admin_platform' (FR-ADM-01).
 */
class TenantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdminPlatform();
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->isAdminPlatform();
    }

    public function create(User $user): bool
    {
        return $user->isAdminPlatform();
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->isAdminPlatform();
    }
}
