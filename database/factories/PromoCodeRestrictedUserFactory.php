<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Buyer;
use App\Models\PromoCode;
use App\Models\PromoCodeRestrictedUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromoCodeRestrictedUser>
 */
class PromoCodeRestrictedUserFactory extends Factory
{
    protected $model = PromoCodeRestrictedUser::class;

    public function definition(): array
    {
        return [
            'promo_code_id' => PromoCode::factory(),
            'buyer_id' => Buyer::factory(),
        ];
    }
}
