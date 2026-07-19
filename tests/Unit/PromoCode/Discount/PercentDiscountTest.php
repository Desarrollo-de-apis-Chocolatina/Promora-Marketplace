<?php

declare(strict_types=1);

namespace Tests\Unit\PromoCode\Discount;

use App\Domain\PromoCode\Discount\PercentDiscount;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\PromoCodeBuilder;

class PercentDiscountTest extends TestCase
{
    public function test_it_calculates_a_percentage_of_the_subtotal(): void
    {
        $strategy = new PercentDiscount;
        $code = (new PromoCodeBuilder)->withType('percent')->withValue(20.0)->build();
        $context = (new OrderContextBuilder)->withSubtotal(150.0)->build();

        $discount = $strategy->calculate($code, $context);

        $this->assertEquals(30.0, $discount);
    }

    public function test_it_returns_zero_when_subtotal_is_zero(): void
    {
        $strategy = new PercentDiscount;
        $code = (new PromoCodeBuilder)->withType('percent')->withValue(15.0)->build();
        $context = (new OrderContextBuilder)->withSubtotal(0.0)->build();

        $discount = $strategy->calculate($code, $context);

        $this->assertEquals(0.0, $discount);
    }
}
