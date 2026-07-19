<?php

declare(strict_types=1);

namespace Tests\Unit\PromoCode\Discount;

use App\Domain\PromoCode\Discount\TieredDiscount;
use App\Domain\PromoCode\ValueObjects\DiscountTier;
use PHPUnit\Framework\TestCase;
use Tests\Factories\BuyerProfileBuilder;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\PromoCodeBuilder;

class TieredDiscountTest extends TestCase
{
    private function tiers(): array
    {
        return [
            new DiscountTier(0, 5.0),
            new DiscountTier(3, 10.0),
            new DiscountTier(10, 15.0),
        ];
    }

    public function test_it_applies_the_base_tier_with_no_completed_orders(): void
    {
        $strategy = new TieredDiscount;
        $code = (new PromoCodeBuilder)->withType('tiered')->withTiers($this->tiers())->build();
        $buyer = (new BuyerProfileBuilder)->withCompletedOrders(0)->build();
        $context = (new OrderContextBuilder)->withSubtotal(100.0)->withBuyerProfile($buyer)->build();

        $discount = $strategy->calculate($code, $context);

        $this->assertEquals(5.0, $discount);
    }

    public function test_it_applies_the_highest_tier_reached(): void
    {
        $strategy = new TieredDiscount;
        $code = (new PromoCodeBuilder)->withType('tiered')->withTiers($this->tiers())->build();
        $buyer = (new BuyerProfileBuilder)->withCompletedOrders(4)->build();
        $context = (new OrderContextBuilder)->withSubtotal(100.0)->withBuyerProfile($buyer)->build();

        $discount = $strategy->calculate($code, $context);

        $this->assertEquals(10.0, $discount);
    }

    public function test_it_applies_the_top_tier_when_threshold_is_exceeded(): void
    {
        $strategy = new TieredDiscount;
        $code = (new PromoCodeBuilder)->withType('tiered')->withTiers($this->tiers())->build();
        $buyer = (new BuyerProfileBuilder)->withCompletedOrders(25)->build();
        $context = (new OrderContextBuilder)->withSubtotal(200.0)->withBuyerProfile($buyer)->build();

        $discount = $strategy->calculate($code, $context);

        $this->assertEquals(30.0, $discount);
    }
}
