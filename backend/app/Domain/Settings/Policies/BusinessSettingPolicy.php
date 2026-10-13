<?php

namespace App\Domain\Settings\Policies;

use App\Domain\Shared\Policies\ContentPolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BusinessSettingPolicy extends ContentPolicy
{
    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, Model $record): bool
    {
        return false;
    }

    public function update(User $user, Model $record): bool
    {
        return $user->isAdministrator();
    }
}
