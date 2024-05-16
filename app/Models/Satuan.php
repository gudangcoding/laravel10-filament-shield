<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Satuan extends Model
{
    use HasFactory;
    protected $fillable = ["type", 'name'];
    public function team(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Team::class);
    }
}
