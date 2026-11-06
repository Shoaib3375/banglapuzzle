<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    private static array $adjectives = [
        'Premium', 'Original', 'Imported', 'Local', 'Branded',
        'Genuine', 'Professional', 'Classic', 'Modern', 'Stylish',
    ];

    private static array $nouns = [
        'Item', 'Product', 'Gadget', 'Accessory', 'Device',
        'Kit', 'Set', 'Pack', 'Bundle', 'Collection',
    ];

    public function definition(): array
    {
        $adj = $this->faker->randomElement(self::$adjectives);
        $noun = $this->faker->randomElement(self::$nouns);
        $brand = $this->faker->company();
        $name = "{$adj} {$brand} {$noun}";

        $oldPrice = $this->faker->numberBetween(100, 10000);
        $newPrice = $this->faker->numberBetween(50, $oldPrice);

        return [
            'category_id' => null, // must be overridden
            'subcategory_id' => null, // must be overridden
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1000, 99999),
            'description' => $this->faker->paragraph(2).' '.$this->faker->sentence(12),
            'image' => 'https://picsum.photos/seed/'.$this->faker->unique()->numberBetween(1, 1000).'/600/600',
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'is_active' => true,
        ];
    }

    /**
     * State: assign a specific category_id and subcategory_id.
     */
    public function forSubcategory(int $categoryId, int $subcategoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
        ]);
    }
}
