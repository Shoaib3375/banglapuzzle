<?php

namespace Database\Factories;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subcategory>
 */
class SubcategoryFactory extends Factory
{
    protected $model = Subcategory::class;

    /**
     * Subcategory names grouped loosely — the seeder picks a random one.
     */
    private static array $subcategoryPool = [
        'Mobile Phones',
        'Laptops & Computers',
        'Cameras & Photography',
        'Audio & Headphones',
        'Smart Watches',
        'Men\'s Clothing',
        'Women\'s Clothing',
        'Footwear',
        'Bags & Luggage',
        'Jewellery & Accessories',
        'Furniture',
        'Kitchen & Dining',
        'Bedding & Bath',
        'Home Décor',
        'Lighting',
        'Skincare',
        'Hair Care',
        'Fragrances',
        'Vitamins & Supplements',
        'Personal Care',
        'Academic Books',
        'Notebooks & Diaries',
        'Art Supplies',
        'Pens & Pencils',
        'Educational Toys',
        'Cricket Equipment',
        'Gym & Fitness',
        'Outdoor Gear',
        'Cycling',
        'Swimming',
        'Rice & Flour',
        'Snacks & Confectionery',
        'Beverages',
        'Dairy & Eggs',
        'Spices & Condiments',
        'Action Figures',
        'Board Games',
        'Building Blocks',
        'Soft Toys',
        'Remote Control Toys',
        'Car Accessories',
        'Motorcycle Parts',
        'Tyres & Wheels',
        'Car Care',
        'Tools & Equipment',
        'Printer & Ink',
        'Desk Accessories',
        'Whiteboards & Boards',
        'Filing & Storage',
        'Chairs & Tables',
    ];

    private static int $index = 0;

    public function definition(): array
    {
        // Requires category_id to be passed via state/create
        $name = self::$subcategoryPool[self::$index % count(self::$subcategoryPool)];
        self::$index++;

        return [
            'category_id' => null, // must be overridden via state
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => $this->faker->sentence(10),
            'is_active'   => true,
        ];
    }

    /**
     * State: assign a specific category_id.
     */
    public function forCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }
}
