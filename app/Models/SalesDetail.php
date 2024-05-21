<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    use HasFactory;
    protected $table = "sales_detail";
    protected $fillable = [
        'sales_order_id',
        'product_id',
        'satuan_id',
        'harga',
        'qty',
        'subtotal'
    ];

    public function salesOrder()
    {
        // return $this->BelongsToMany(\App\Models\SalesOrder::class);
        return $this->BelongsToMany(\App\Models\SalesOrder::class, 'sales_id');
    }
}
