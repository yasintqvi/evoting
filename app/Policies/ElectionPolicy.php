<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Election;
use App\Models\User;

class ElectionPolicy
{
    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, Election $election): bool
    {
        return true;
    }

    /**
     * Determine if the user can vote in the election.
     */
    public function vote(User $user, Election $election): bool
    {
        // مدیر سایت همیشه می‌تواند رای بدهد
        if ($user->hasRole(Role::Manager->value)) {
            return true;
        }

        return true;
    }

    /**
     * Determine if the user can access the election for voting.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
}

