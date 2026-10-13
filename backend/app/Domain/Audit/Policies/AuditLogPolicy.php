<?php

namespace App\Domain\Audit\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdministrator();
    }

    public function view(User $user, Model $record): bool
    {
        return $user->isAdministrator();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Model $record): bool
    {
        return false;
    }

    public function delete(User $user, Model $record): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
