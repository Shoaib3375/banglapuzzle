<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Realistic Bangladeshi e-commerce category names.
     */
    private static array $categoryNames = [
        'Electronics',
        'Fashion',
        'Home & Living',
        'Beauty & Health',
        'Books & Stationery',
        'Sports & Outdoors',
        'Food & Groceries',
        'Toys & Games',
        'Automotive',
        'Office Supplies',
    ];

    private static int $index = 0;

    public function definition(): array
    {
        // Cycle through the fixed list so 5 seeded categories are distinct
        $name = self::$categoryNames[self::$index % count(self::$categoryNames)];
        self::$index++;

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->sentence(10),
            'is_active'   => true,
        ];
    }
}
