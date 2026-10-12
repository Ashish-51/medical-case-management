<?php

namespace App\Policies;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'viewer', 'engineer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalCase $medicalCase): bool
    {
        return in_array($user->role, ['admin', 'viewer', 'engineer']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'engineer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalCase $medicalCase): bool
    {
        return in_array($user->role, ['admin', 'engineer']);
    }

    /**
     * Determine whether the user can change the status of the model.
     */
    public function changeStatus(User $user, MedicalCase $medicalCase): bool
    {
        return in_array($user->role, ['admin', 'engineer']);
    }

    /**
     * Determine whether the user can delete the model.
     * Note: Per requirements, Delete must work for Admin ONLY!
     */
    public function delete(User $user, MedicalCase $medicalCase): bool
    {
        return $user->role === 'admin';
    }
}
