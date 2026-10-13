<?php

namespace App\Domain\Shared\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && ! $user->trashed();
    }

    public function view(User $user, Model $record): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Model $record): bool
    {
        return $this->viewAny($user) && (! method_exists($record, 'trashed') || ! $record->trashed() || $user->isAdministrator());
    }

    public function delete(User $user, Model $record): bool
    {
        return $user->isAdministrator();
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Model $record): bool
    {
        return $user->isAdministrator();
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return $user->isAdministrator() && $record->trashed();
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
