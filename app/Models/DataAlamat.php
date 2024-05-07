<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DataAlamat extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "team_id",
        "nama",
        "no_hp",
        "alamat",
        "kelurahan",
        "kec",
        "kel",
        "kab",
        "prov",
        "kodepos",
        "tipe",
        "nama_bank",
        "no_rekening",
        "atas_nama",
        "nama_toko",

    ];

    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class);
    }
}
