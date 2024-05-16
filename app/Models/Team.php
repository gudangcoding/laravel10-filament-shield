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
        return $this->HasMany(Category::class);
    }

    public function role(): HasMany
    {
        return $this->HasMany(Role::class);
    }


    public function produk(): HasMany
    {
        return $this->HasMany(Product::class);
    }
    public function invoice(): HasMany
    {
        return $this->HasMany(Invoice::class);
    }
    public function mutasi_bank(): HasMany
    {
        return $this->HasMany(MutasiBank::class);
    }
    public function customers(): HasMany
    {
        return $this->HasMany(MutasiBank::class);
    }
    public function satuan(): HasMany
    {
        return $this->HasMany(Satuan::class);
    }
}
