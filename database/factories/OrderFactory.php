<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Buyer;
use App\Models\Order;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'buyer_id' => Buyer::factory(),
            'subtotal' => fake()->randomFloat(2, 10, 500),
            'category_id' => ServiceCategory::factory(),
            'order_status' => 'draft',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'completed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'cancelled',
        ]);
    }

    public function inProcess(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_status' => 'in_process',
        ]);
    }
}
