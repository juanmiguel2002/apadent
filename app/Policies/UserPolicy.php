<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function create(User $user)
    {
        return $user->hasRole(['admin', 'doctor_admin']);
    }

    public function update(User $user)
    {
        return $user->hasRole(['admin', 'doctor_admin']);
    }
}
