<?php

namespace App\Domain\Content\Policies;

use App\Domain\Shared\Policies\ContentPolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PagePolicy extends ContentPolicy
{
    public function create(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Model $record): bool
    {
        return false;
    }
}
