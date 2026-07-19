<?php

declare(strict_types=1);

namespace Tests\Unit\PromoCode\Discount;

use App\Domain\PromoCode\Discount\DiscountCalculator;
use App\Domain\PromoCode\ValueObjects\DiscountTier;
use PHPUnit\Framework\TestCase;
use Tests\Factories\BuyerProfileBuilder;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\PromoCodeBuilder;

class DiscountCalculatorTest extends TestCase
{
    public function test_it_delegates_to_the_fixed_strategy(): void
    {
        $calculator = new DiscountCalculator;
        $code = (new PromoCodeBuilder)->withType('fixed')->withValue(20.0)->build();
        $context = (new OrderContextBuilder)->withSubtotal(100.0)->build();

        $this->assertEquals(20.0, $calculator->calculate($code, $context));
    }

    public function test_it_delegates_to_the_percent_strategy(): void
    {
        $calculator = new DiscountCalculator;
        $code = (new PromoCodeBuilder)->withType('percent')->withValue(10.0)->build();
        $context = (new OrderContextBuilder)->withSubtotal(100.0)->build();

        $this->assertEquals(10.0, $calculator->calculate($code, $context));
    }

    public function test_it_delegates_to_the_tiered_strategy(): void
    {
        $calculator = new DiscountCalculator;
        $code = (new PromoCodeBuilder)
            ->withType('tiered')
            ->withTiers([new DiscountTier(0, 5.0)])
            ->build();
        $buyer = (new BuyerProfileBuilder)->withCompletedOrders(0)->build();
        $context = (new OrderContextBuilder)->withSubtotal(100.0)->withBuyerProfile($buyer)->build();

        $this->assertEquals(5.0, $calculator->calculate($code, $context));
    }

    public function test_it_throws_for_an_unknown_discount_type(): void
    {
        $calculator = new DiscountCalculator;
        $code = (new PromoCodeBuilder)->withType('unknown')->build();
        $context = (new OrderContextBuilder)->build();

        $this->expectException(\InvalidArgumentException::class);

        $calculator->calculate($code, $context);
    }
}
