<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\MemberRole;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'email',
        'role',
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';

    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'role' => MemberRole::class,
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role->isMember();
    }

    public function getFilamentName(): string
    {
        return explode('.', $this->email)[0];
    }
}
