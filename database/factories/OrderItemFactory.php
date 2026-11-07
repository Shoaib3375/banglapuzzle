<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $oldPrice = $this->faker->numberBetween(200, 2000);
        $price = $this->faker->numberBetween(100, $oldPrice);
        $quantity = $this->faker->numberBetween(1, 5);
        $subtotal = $price * $quantity;

        return [
            'order_id' => Order::factory(),
            'product_id' => null,
            'product_name' => $this->faker->words(3, true),
            'product_slug' => $this->faker->slug(3),
            'product_image' => null,
            'price' => $price,
            'old_price' => $oldPrice,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ];
    }
}
