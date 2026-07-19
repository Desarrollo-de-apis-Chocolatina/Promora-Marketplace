<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PromoCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromoCode>
 */
class PromoCodeFactory extends Factory
{
    protected $model = PromoCode::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('PROMO-####')),
            'discount_type' => 'percent',
            'discount_value' => 10.0,
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
            'max_discount_amount' => null,
        ];
    }

    public function fixed(float $value = 20.0): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_type' => 'fixed',
            'discount_value' => $value,
        ]);
    }

    public function percent(float $value = 10.0): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_type' => 'percent',
            'discount_value' => $value,
        ]);
    }

    public function tiered(): static
    {
        return $this->state(fn (array $attributes) => [
            'discount_type' => 'tiered',
            'discount_value' => null,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'draft']);
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'paused']);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'starts_at' => now()->subMonths(2),
            'expires_at' => now()->subMonth(),
        ]);
    }
}
