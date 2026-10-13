<?php

namespace App\Models;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $attributes = ['role' => 'editor', 'is_active' => true];

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && ! $this->trashed() && in_array($this->role, ['admin', 'editor'], true);
    }

    public function isAdministrator(): bool
    {
        return $this->canAccessPanel(Filament::getPanel('admin')) && $this->role === 'admin';
    }
}
