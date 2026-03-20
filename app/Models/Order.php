<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'subtotal',
        'tax',
        'total',
        'currency',
        'idempotency_key'
    ];

    public function items(): HasMany{
        return $this->hasMany(OrderItem::class);
    }
}
