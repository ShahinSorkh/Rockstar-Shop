<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float'
    ];

    public function customizations()
    {
        return $this->hasMany(ProductCustomization::class);
    }

    protected static function booted()
    {
        static::created(function (Product $product) {
            $product->customizations()->create([
                'name' => 'Consume location',
                'options' => ['take away', 'in shop'],
            ]);
        });
    }
}
