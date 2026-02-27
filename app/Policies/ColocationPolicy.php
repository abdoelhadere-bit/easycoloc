<?php

namespace App\Policies;

use App\Models\Colocation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ColocationPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Colocation $colocation): bool
    {
        return $colocation->members()
                        ->where('users.id', $user->id)
                        ->wherePivotNull('left_at')
                        ->exists();
    }
    
    

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Colocation $colocation): bool
    {
        return $colocation->owner_id === $user->id;;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Colocation $colocation): bool
    {
        return $colocation->owner_id === $user->id;;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Colocation $colocation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Colocation $colocation): bool
    {
        return false;
    }

    public function invite(User $user, Colocation $colocation): bool
    {
        return $colocation->owner_id === $user->id;
    }

    public function manageCategories(User $user, Colocation $colocation): bool
    {
        return $colocation->owner_id === $user->id;
    }
}
