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
        'product_id',
        'product_name',
        'product_slug',
        'product_image',
        'price',
        'old_price',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    // --------------- Relationships ---------------

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    // --------------- Accessors ---------------

    public function getDiscountPercentageAttribute(): ?int
    {
        $old = (float) $this->old_price;
        $new = (float) $this->price;

        if ($old > 0 && $new > 0 && $old > $new) {
            return (int) round((($old - $new) / $old) * 100);
        }

        return null;
    }
}
