<?php

namespace App\Policies;

use App\Models\User;

/**
 * Solo el super admin administra usuarios. Cada webmaster edita únicamente su propio
 * perfil desde el menú de su cuenta (página de perfil de Filament), no desde este módulo.
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isSuperAdmin();
    }

    /** El super admin no se puede eliminar, ni siquiera a sí mismo. */
    public function delete(User $user, User $model): bool
    {
        return $user->isSuperAdmin() && ! $model->isSuperAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}
