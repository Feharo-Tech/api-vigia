<?php

namespace App\Policies;

use App\Models\Api;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApiPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Api $api): bool
    {
        return $user->id === $api->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Api $api): bool
    {
        return $user->id === $api->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Api $api): bool
    {
        return $user->id === $api->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Api $api): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Api $api): bool
    {
        return false;
    }

    public function reset(User $user, Api $api): bool
    {
        return $user->id === $api->user_id;
    }
}
