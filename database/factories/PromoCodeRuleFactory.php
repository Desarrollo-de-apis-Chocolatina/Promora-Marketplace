<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PromoCode;
use App\Models\PromoCodeRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromoCodeRule>
 */
class PromoCodeRuleFactory extends Factory
{
    protected $model = PromoCodeRule::class;

    public function definition(): array
    {
        return [
            'promo_code_id' => PromoCode::factory(),
            'rule_type' => 'min_purchase_amount',
            'parameters' => ['minAmount' => 50.0],
            'is_active' => true,
        ];
    }
}
