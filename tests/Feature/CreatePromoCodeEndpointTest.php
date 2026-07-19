<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Buyer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePromoCodeEndpointTest extends TestCase
{
    use RefreshDatabase;

    private function endpoint(): string
    {
        return '/api/promo-codes';
    }

    public function test_it_rejects_an_invalid_payload(): void
    {
        $response = $this->postJson($this->endpoint(), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code', 'discount_type', 'starts_at', 'expires_at']);
    }

    public function test_it_creates_a_fixed_promo_code(): void
    {
        $response = $this->postJson($this->endpoint(), [
            'code' => 'WELCOME20',
            'discount_type' => 'fixed',
            'discount_value' => 20,
            'starts_at' => now()->toDateString(),
            'expires_at' => now()->addMonth()->toDateString(),
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'code' => 'WELCOME20',
            'discount_type' => 'fixed',
        ]);

        $this->assertDatabaseHas('promo_codes', [
            'code' => 'WELCOME20',
            'discount_type' => 'fixed',
            'status' => 'draft',
        ]);
    }

    public function test_it_creates_a_promo_code_with_configurable_rules(): void
    {
        $response = $this->postJson($this->endpoint(), [
            'code' => 'MIN100',
            'discount_type' => 'percent',
            'discount_value' => 15,
            'starts_at' => now()->toDateString(),
            'expires_at' => now()->addMonth()->toDateString(),
            'rules' => [
                ['rule_type' => 'min_purchase_amount', 'parameters' => ['minAmount' => 100]],
            ],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('promo_code_rules', [
            'rule_type' => 'min_purchase_amount',
        ]);
    }

    public function test_it_creates_a_tiered_promo_code_with_tiers(): void
    {
        $response = $this->postJson($this->endpoint(), [
            'code' => 'TIERED',
            'discount_type' => 'tiered',
            'starts_at' => now()->toDateString(),
            'expires_at' => now()->addMonth()->toDateString(),
            'tiers' => [
                ['min_completed_orders' => 0, 'discount_percent' => 5],
                ['min_completed_orders' => 3, 'discount_percent' => 10],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseCount('promo_code_tiers', 2);
    }

    public function test_it_creates_a_restricted_promo_code_with_authorized_buyers(): void
    {
        $buyer = Buyer::factory()->create();

        $response = $this->postJson($this->endpoint(), [
            'code' => 'VIPONLY',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'starts_at' => now()->toDateString(),
            'expires_at' => now()->addMonth()->toDateString(),
            'rules' => [
                ['rule_type' => 'restricted_usage'],
            ],
            'restricted_user_ids' => [$buyer->id],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('promo_code_restricted_users', [
            'buyer_id' => $buyer->id,
        ]);
    }

    public function test_it_rejects_a_duplicate_code(): void
    {
        $this->postJson($this->endpoint(), [
            'code' => 'DUPLICATE',
            'discount_type' => 'fixed',
            'discount_value' => 10,
            'starts_at' => now()->toDateString(),
            'expires_at' => now()->addMonth()->toDateString(),
        ])->assertStatus(201);

        $response = $this->postJson($this->endpoint(), [
            'code' => 'DUPLICATE',
            'discount_type' => 'fixed',
            'discount_value' => 10,
            'starts_at' => now()->toDateString(),
            'expires_at' => now()->addMonth()->toDateString(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code']);
    }
}
