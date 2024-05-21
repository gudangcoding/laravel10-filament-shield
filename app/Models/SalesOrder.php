<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_no',
        'team_id',
        'user_id',
        'satuan_id',
        'total_amount',
        'total_barang',
    ];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

    public function order_details()
    {
        return $this->hasMany(\App\Models\SalesDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
    }

    public function SalesDetail(): HasMany
    {
        return $this->hasMany(SalesDetail::class);
    }
}
