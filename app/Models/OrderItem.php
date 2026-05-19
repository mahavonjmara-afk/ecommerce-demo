<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'quantity', 'price'
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2, ',', ' ') . ' €';
    }
}