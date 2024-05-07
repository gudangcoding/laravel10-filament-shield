<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class Team extends Model
{
    use HasFactory;
    protected $fillable = [
        "name", "slug"
    ];

    public function members(): BelongsToMany
    {
        return $this->BelongsToMany(User::class);
    }
    public function dataalamat(): HasMany
    {
        return $this->HasMany(DataAlamat::class);
    }
    public function categories(): HasMany
    {
        return $this->HasMany(DataAlamat::class);
    }

    public function role(): HasMany
    {
        return $this->HasMany(Role::class);
    }

    // public function roles(): HasMany
    // {
    //     return $this->hasMany(Role::class);
    // }
}