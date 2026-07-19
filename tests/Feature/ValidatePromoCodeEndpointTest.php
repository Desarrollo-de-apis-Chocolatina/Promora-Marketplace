<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Buyer;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\PromoCodeRestrictedUser;
use App\Models\PromoCodeRule;
use App\Models\PromoCodeTier;
use App\Models\PromoCodeUsage;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidatePromoCodeEndpointTest extends TestCase
{
    use RefreshDatabase;

    private function endpoint(): string
    {
        return '/api/promo-codes/validate';
    }

    public function test_it_rejects_an_invalid_payload(): void
    {
        $response = $this->postJson($this->endpoint(), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code', 'order.subtotal', 'order.category_id', 'buyer.id']);
    }

    public function test_it_rejects_a_nonexistent_code(): void
    {
        $category = ServiceCategory::factory()->create();
        $buyer = Buyer::factory()->create();

        $response = $this->postJson($this->endpoint(), [
            'code' => 'DOES-NOT-EXIST',
            'order' => ['id' => 'ORD-1', 'subtotal' => 100, 'category_id' => $category->id],
            'buyer' => ['id' => $buyer->id],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['valid' => false, 'error' => 'invalid_code']);
    }

    public function test_it_rejects_an_expired_code(): void
    {
        $category = ServiceCategory::factory()->create();
        $buyer = Buyer::factory()->create();
        $promoCode = PromoCode::factory()->percent(10)->expired()->create(['code' => 'OLD20']);

        $response = $this->postJson($this->endpoint(), [
            'code' => $promoCode->code,
            'order' => ['id' => 'ORD-1', 'subtotal' => 100, 'category_id' => $category->id],
            'buyer' => ['id' => $buyer->id],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['valid' => false, 'error' => 'expired_coupon']);
    }

    public function test_it_rejects_when_a_configurable_rule_fails(): void
    {
        $category = ServiceCategory::factory()->create();
        $buyer = Buyer::factory()->create();
        $promoCode = PromoCode::factory()->percent(10)->create(['code' => 'MIN100']);

        PromoCodeRule::factory()->create([
            'promo_code_id' => $promoCode->id,
            'rule_type' => 'min_purchase_amount',
            'parameters' => ['minAmount' => 100.0],
        ]);

        $response = $this->postJson($this->endpoint(), [
            'code' => $promoCode->code,
            'order' => ['id' => 'ORD-1', 'subtotal' => 50, 'category_id' => $category->id],
            'buyer' => ['id' => $buyer->id],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['valid' => false, 'error' => 'min_amount_required']);
    }

    public function test_it_validates_a_fixed_discount_code(): void
    {
        $category = ServiceCategory::factory()->create();
        $buyer = Buyer::factory()->create();
        $promoCode = PromoCode::factory()->fixed(20)->create(['code' => 'FIXED20']);

        $response = $this->postJson($this->endpoint(), [
            'code' => $promoCode->code,
            'order' => ['id' => 'ORD-1', 'subtotal' => 150, 'category_id' => $category->id],
            'buyer' => ['id' => $buyer->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'code' => 'FIXED20',
            'discount' => 20,
            'subtotal' => 150,
            'total' => 130,
            'error' => null,
        ]);
    }

    public function test_it_validates_a_percent_discount_code(): void
    {
        $category = ServiceCategory::factory()->create();
        $buyer = Buyer::factory()->create();
        $promoCode = PromoCode::factory()->percent(20)->create(['code' => 'PERCENT20']);

        $response = $this->postJson($this->endpoint(), [
            'code' => $promoCode->code,
            'order' => ['id' => 'ORD-1', 'subtotal' => 150, 'category_id' => $category->id],
            'buyer' => ['id' => $buyer->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'discount' => 30,
            'total' => 120,
        ]);
    }

    public function test_it_validates_a_tiered_discount_code_based_on_completed_orders(): void
    {
        $category = ServiceCategory::factory()->create();
        $buyer = Buyer::factory()->create();
        $promoCode = PromoCode::factory()->tiered()->create(['code' => 'TIERED']);

        PromoCodeTier::factory()->create(['promo_code_id' => $promoCode->id, 'min_completed_orders' => 0, 'discount_percent' => 5]);
        PromoCodeTier::factory()->create(['promo_code_id' => $promoCode->id, 'min_completed_orders' => 3, 'discount_percent' => 10]);

        Order::factory()->count(3)->completed()->create(['buyer_id' => $buyer->id, 'category_id' => $category->id]);

        $response = $this->postJson($this->endpoint(), [
            'code' => $promoCode->code,
            'order' => ['id' => 'ORD-1', 'subtotal' => 200, 'category_id' => $category->id],
            'buyer' => ['id' => $buyer->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'discount' => 20,
            'total' => 180,
        ]);
    }

    public function test_it_applies_the_maximum_discount_cap(): void
    {
        $category = ServiceCategory::factory()->create();
        $buyer = Buyer::factory()->create();
        $promoCode = PromoCode::factory()->percent(50)->create([
            'code' => 'CAPPED',
            'max_discount_amount' => 25,
        ]);

        $response = $this->postJson($this->endpoint(), [
            'code' => $promoCode->code,
            'order' => ['id' => 'ORD-1', 'subtotal' => 200, 'category_id' => $category->id],
            'buyer' => ['id' => $buyer->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'discount' => 25,
            'total' => 175,
        ]);
    }

    public function test_it_rejects_a_restricted_code_for_an_unauthorized_buyer(): void
    {
        $category = ServiceCategory::factory()->create();
        $buyer = Buyer::factory()->create();
        $authorizedBuyer = Buyer::factory()->create();
        $promoCode = PromoCode::factory()->percent(10)->create(['code' => 'VIPONLY']);

        PromoCodeRule::factory()->create([
            'promo_code_id' => $promoCode->id,
            'rule_type' => 'restricted_usage',
            'parameters' => [],
        ]);
        PromoCodeRestrictedUser::factory()->create([
            'promo_code_id' => $promoCode->id,
            'buyer_id' => $authorizedBuyer->id,
        ]);

        $response = $this->postJson($this->endpoint(), [
            'code' => $promoCode->code,
            'order' => ['id' => 'ORD-1', 'subtotal' => 100, 'category_id' => $category->id],
            'buyer' => ['id' => $buyer->id],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['valid' => false, 'error' => 'restricted_usage']);
    }

    public function test_it_rejects_when_the_global_usage_limit_was_reached(): void
    {
        $category = ServiceCategory::factory()->create();
        $buyer = Buyer::factory()->create();
        $promoCode = PromoCode::factory()->percent(10)->create(['code' => 'LIMITED']);

        PromoCodeRule::factory()->create([
            'promo_code_id' => $promoCode->id,
            'rule_type' => 'global_usage_limit',
            'parameters' => ['limit' => 1],
        ]);

        $order = Order::factory()->create(['buyer_id' => $buyer->id, 'category_id' => $category->id]);
        PromoCodeUsage::factory()->create([
            'promo_code_id' => $promoCode->id,
            'order_id' => $order->id,
            'buyer_id' => $buyer->id,
            'payment_status' => 'paid',
        ]);

        $response = $this->postJson($this->endpoint(), [
            'code' => $promoCode->code,
            'order' => ['id' => 'ORD-2', 'subtotal' => 100, 'category_id' => $category->id],
            'buyer' => ['id' => $buyer->id],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['valid' => false, 'error' => 'usage_limit_reached']);
    }
}
