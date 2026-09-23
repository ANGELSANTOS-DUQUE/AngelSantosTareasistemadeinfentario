<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
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
     * Determine if the user can view any items.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('catalogo.view');
    }

    /**
     * Determine if the user can view the item (same empresa).
     */
    public function view(User $user, Item $item): bool
    {
        return $user->can('catalogo.view')
            && $user->empresa_id === $item->empresa_id;
    }

    /**
     * Determine if the user can create items.
     */
    public function create(User $user): bool
    {
        return $user->can('catalogo.create');
    }

    /**
     * Determine if the user can update the item (same empresa).
     */
    public function update(User $user, Item $item): bool
    {
        return $user->can('catalogo.edit')
            && $user->empresa_id === $item->empresa_id;
    }

    /**
     * Determine if the user can delete the item (same empresa).
     */
    public function delete(User $user, Item $item): bool
    {
        return $user->can('catalogo.delete')
            && $user->empresa_id === $item->empresa_id;
    }
}
