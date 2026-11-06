<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'description',
        'image',
        'old_price',
        'new_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'old_price'  => 'decimal:2',
            'new_price'  => 'decimal:2',
        ];
    }

    // --------------- Boot (auto-slug) ---------------

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        static::updating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

    /**
     * Generate a unique slug for the products table.
     */
    public static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $n    = 1;

        while (true) {
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            if (! $query->exists()) {
                break;
            }
            $slug = $base . '-' . $n;
            $n++;
        }

        return $slug;
    }

    // --------------- Accessors ---------------

    /**
     * Return discount percentage (integer) when old_price > new_price > 0, else null.
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        $old = (float) $this->old_price;
        $new = (float) $this->new_price;

        if ($old > 0 && $new > 0 && $old > $new) {
            return (int) round((($old - $new) / $old) * 100);
        }

        return null;
    }

    // --------------- Relationships ---------------

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    // --------------- Scopes ---------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
