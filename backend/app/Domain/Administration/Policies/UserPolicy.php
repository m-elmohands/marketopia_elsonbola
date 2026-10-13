<?php

namespace App\Domain\Administration\Policies;

use App\Domain\Shared\Policies\ContentPolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends ContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Model $record): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Model $record): bool
    {
        return $this->viewAny($user) && $user->id !== $record->id;
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return false;
    }
}
