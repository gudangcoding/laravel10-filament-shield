<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPanelShield;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }
    public function canAccessPanel(Panel $panel): bool
    {
        // Contoh implementasi sederhana: mengizinkan akses ke semua panel
        return true;
    }
    // public function team()
    // {
    //     return $this->belongsTo(Team::class);
    // }

    public function team()
    {
        return $this->teams()->where('team_id', Filament::getTenant()->id);
    }

    // public function roles(): BelongsToMany
    // {
    //     return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'model_has_roles', 'model_id', 'role_id')
    //         ->where('model_type', User::class);
    // }
}