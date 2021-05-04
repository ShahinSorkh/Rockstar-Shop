<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const STATUS_WAITING = 'waiting';
    const STATUS_PREPARATION = 'preparation';
    const STATUS_READY = 'ready';
    const STATUS_DELEVERED = 'delivered';
    const STATUSES = [
        self::STATUS_WAITING,
        self::STATUS_PREPARATION,
        self::STATUS_READY,
        self::STATUS_DELEVERED,
    ];

    protected $fillable = [
        'user_id',
        'product_id',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'float',
    ];

    public function customizations()
    {
        return $this->hasMany(OrderCustomization::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
