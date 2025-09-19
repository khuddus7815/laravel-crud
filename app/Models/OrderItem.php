<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'price',
    ];

    /**
     * Define the relationship to the Order model.
     * An OrderItem belongs to an Order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * ADD THIS METHOD
     * Define the relationship to the Item model.
     * An OrderItem belongs to an Item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}