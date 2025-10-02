<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function view(User $user, Event $event): bool
    {
        return $event->organization->owner_id === $user->id || $user->isAdmin();
    }

    public function update(User $user, Event $event): bool
    {
        return $event->organization->owner_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Event $event): bool
    {
        return $event->organization->owner_id === $user->id || $user->isAdmin();
    }
}
