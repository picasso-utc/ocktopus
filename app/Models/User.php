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

    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'role' => MemberRole::class,
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->role->isMember();
        } else if ($panel->getId() === 'treso') {
            return $this->role->isAdministrator();
        } else if ($panel->getId() === 'public') {
            return true;
        }
        return false;
    }
    public function getFilamentName(): string
    {
        return explode('.', $this->email)[0];
    }

    public function getNombrePointsAttribute()
    {
        // Récupérer toutes les astreintes de l'utilisateur
        $astreintes = Astreinte::where('member_id', $this->id)->get();

        // Calculer le nombre total de points en utilisant la fonction définie
        $nombrePoints = $astreintes->sum(function ($astreinte) {
            return $astreinte->points;
        });

        return $nombrePoints;
    }
}
