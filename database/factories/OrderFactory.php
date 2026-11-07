<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(200, 5000);
        $deliveryCharge = $subtotal >= 2000 ? 0 : 80;
        $total = $subtotal + $deliveryCharge;

        return [
            'user_id' => null,
            'order_number' => 'BP-'.strtoupper($this->faker->unique()->lexify('????????')),
            'full_name' => $this->faker->name(),
            'phone' => '01'.rand(3, 9).$this->faker->numerify('########'),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'district' => $this->faker->randomElement(['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna']),
            'postcode' => $this->faker->optional()->numerify('####'),
            'landmark' => $this->faker->optional()->sentence(3),
            'note' => $this->faker->optional()->sentence(),
            'payment_method' => $this->faker->randomElement(['cod', 'bkash', 'nagad']),
            'subtotal' => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'total' => $total,
            'status' => Order::STATUS_PENDING,
        ];
    }

    /**
     * State: assign a specific user_id.
     */
    public function forUser(int $userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * State: confirmed order.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_CONFIRMED,
        ]);
    }

    /**
     * State: delivered order.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_DELIVERED,
        ]);
    }
}
