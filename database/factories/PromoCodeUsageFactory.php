<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Buyer;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromoCodeUsage>
 */
class PromoCodeUsageFactory extends Factory
{
    protected $model = PromoCodeUsage::class;

    public function definition(): array
    {
        return [
            'promo_code_id' => PromoCode::factory(),
            'order_id' => Order::factory(),
            'buyer_id' => Buyer::factory(),
            'discount_amount' => fake()->randomFloat(2, 1, 50),
            'payment_status' => 'paid',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['payment_status' => 'pending']);
    }
}
