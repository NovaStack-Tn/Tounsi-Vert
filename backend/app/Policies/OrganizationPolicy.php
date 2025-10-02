<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function view(User $user, Organization $organization): bool
    {
        return $organization->owner_id === $user->id || $user->isAdmin();
    }

    public function update(User $user, Organization $organization): bool
    {
        return $organization->owner_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $organization->owner_id === $user->id || $user->isAdmin();
    }
}
