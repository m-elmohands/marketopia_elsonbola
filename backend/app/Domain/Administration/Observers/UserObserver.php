<?php

namespace App\Domain\Administration\Observers;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserObserver
{
    public function saving(User $user): void
    {
        if ($user->exists && ($user->role !== 'admin' || ! $user->is_active)) {
            $this->ensureAnotherAdministrator($user);
        }
    }

    public function deleting(User $user): void
    {
        $this->ensureAnotherAdministrator($user);
    }

    private function ensureAnotherAdministrator(User $user): void
    {
        if ($user->getOriginal('role') === 'admin' && $user->getOriginal('is_active') && ! User::where('role', 'admin')->where('is_active', true)->whereKeyNot($user->id)->exists()) {
            throw ValidationException::withMessages(['role' => __('Keep at least one active administrator.')]);
        }
    }
}
