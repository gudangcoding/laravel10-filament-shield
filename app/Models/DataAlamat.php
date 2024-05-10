<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DataAlamat extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'tipe',
        'no_hp',
        'alamat',
        'nama_bank',
        'no_rekening',
        'atas_nama',
        'nama_toko',
    ];

    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class);
    }
}
