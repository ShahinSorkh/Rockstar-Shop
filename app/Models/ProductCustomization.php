<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCustomization extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'name', 'options',
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
