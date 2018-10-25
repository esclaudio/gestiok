<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends Policy
{
    public function create(User $user): bool
    {
        return $user->is_admin;
    }
    
    public function edit(User $user, User $theUser): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user, User $theUser): bool
    {
        return $user->is_admin;
    }
}
