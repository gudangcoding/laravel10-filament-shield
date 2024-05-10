<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        // Pastikan untuk menambahkan semua kolom yang relevan
    ];

    /**
     * Mendefinisikan relasi ke model Team.
     */
    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class);
    }
}
