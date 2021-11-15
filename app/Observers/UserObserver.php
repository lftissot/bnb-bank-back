<?php

namespace App\Observers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    public function created(User $user) {
        $user->assignRole(Role::findByName("Customer", "api"));
    }
}
