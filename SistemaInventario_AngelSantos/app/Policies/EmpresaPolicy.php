<?php

namespace App\Policies;

use App\Models\Empresa;
use App\Models\User;

class EmpresaPolicy
{
    /**
     * Super Admin bypasses all checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Only super admin can view any company (handled by before).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('empresas.view');
    }

    /**
     * Admin of the company can view their own company.
     */
    public function view(User $user, Empresa $empresa): bool
    {
        return $user->can('empresas.view')
            && $user->empresa_id === $empresa->id;
    }

    /**
     * Only super admins can create companies (handled by before).
     */
    public function create(User $user): bool
    {
        return false; // Only super_admin (covered by before())
    }

    /**
     * Admin can edit their own empresa.
     */
    public function update(User $user, Empresa $empresa): bool
    {
        return $user->can('empresas.edit')
            && $user->empresa_id === $empresa->id;
    }

    /**
     * Only super admin can delete (handled by before).
     */
    public function delete(User $user, Empresa $empresa): bool
    {
        return false; // Only super_admin (covered by before())
    }
}
