<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // --------------- Boot (auto-slug) ---------------

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Subcategory $subcategory) {
            if (empty($subcategory->slug)) {
                $subcategory->slug = static::generateUniqueSlug(
                    $subcategory->name,
                    $subcategory->category_id
                );
            }
        });

        static::updating(function (Subcategory $subcategory) {
            if (empty($subcategory->slug)) {
                $subcategory->slug = static::generateUniqueSlug(
                    $subcategory->name,
                    $subcategory->category_id,
                    $subcategory->id
                );
            }
        });
    }

    /**
     * Generate a unique slug within the same category.
     */
    public static function generateUniqueSlug(
        string $name,
        int $categoryId,
        ?int $excludeId = null
    ): string {
        $base = Str::slug($name);
        $slug = $base;
        $n    = 1;

        while (true) {
            $query = static::where('category_id', $categoryId)
                           ->where('slug', $slug);
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

    // --------------- Relationships ---------------

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // --------------- Scopes ---------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
