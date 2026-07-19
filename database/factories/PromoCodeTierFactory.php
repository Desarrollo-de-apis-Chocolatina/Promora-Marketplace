<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PromoCode;
use App\Models\PromoCodeTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromoCodeTier>
 */
class PromoCodeTierFactory extends Factory
{
    protected $model = PromoCodeTier::class;

    public function definition(): array
    {
        return [
            'promo_code_id' => PromoCode::factory(),
            'min_completed_orders' => 0,
            'discount_percent' => 5.0,
        ];
    }
}
