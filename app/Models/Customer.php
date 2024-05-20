<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'kode_customer',
        'nama_customer',
        'daerah_customer',
        'class',
        'category',
        'sisa_limit_hutang',
        'total_hutang',
        'hutang_dlm_tempo',
        'hutang_lewat_tempo',
        'limit_nota',
        'limit_hutang',
        'jenis_badan_usaha',
        'uuid_customer',
        'team_id',
        'user_id'
    ];

    public function contacts()
    {
        return $this->hasMany(Contacts::class);
    }

    public function banks()
    {
        return $this->hasMany(Bank::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
