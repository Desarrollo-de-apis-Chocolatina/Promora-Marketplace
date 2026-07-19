<?php

declare(strict_types=1);

namespace Tests\Unit\PromoCode\Discount;

use App\Domain\PromoCode\Discount\FixedDiscount;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\PromoCodeBuilder;

class FixedDiscountTest extends TestCase
{
    public function test_it_applies_the_fixed_amount_when_below_the_subtotal(): void
    {
        $strategy = new FixedDiscount;
        $code = (new PromoCodeBuilder)->withType('fixed')->withValue(20.0)->build();
        $context = (new OrderContextBuilder)->withSubtotal(150.0)->build();

        $discount = $strategy->calculate($code, $context);

        $this->assertEquals(20.0, $discount);
    }

    public function test_it_never_exceeds_the_subtotal(): void
    {
        $strategy = new FixedDiscount;
        $code = (new PromoCodeBuilder)->withType('fixed')->withValue(50.0)->build();
        $context = (new OrderContextBuilder)->withSubtotal(30.0)->build();

        $discount = $strategy->calculate($code, $context);

        $this->assertEquals(30.0, $discount);
    }
}
