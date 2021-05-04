<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCustomization extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_customization_id',
        'option',
    ];

    public function product_customization()
    {
        return $this->belongsTo(ProductCustomization::class);
    }
}
