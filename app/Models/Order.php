<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';

    const STATUS_CONFIRMED = 'confirmed';

    const STATUS_PROCESSING = 'processing';

    const STATUS_SHIPPED = 'shipped';

    const STATUS_DELIVERED = 'delivered';

    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'order_number',
        'full_name',
        'phone',
        'email',
        'address',
        'city',
        'district',
        'postcode',
        'landmark',
        'note',
        'payment_method',
        'subtotal',
        'delivery_charge',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'delivery_charge' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    // --------------- Relationships ---------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // --------------- Static helpers ---------------

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'BP-'.strtoupper(Str::random(8));
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    // --------------- Scopes ---------------

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // --------------- Accessors ---------------

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'gray',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_PROCESSING => 'blue',
            self::STATUS_SHIPPED => 'blue',
            self::STATUS_DELIVERED => 'emerald',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }
}
