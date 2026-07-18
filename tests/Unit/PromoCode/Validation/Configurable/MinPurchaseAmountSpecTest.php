<?php

namespace Tests\Unit\PromoCode\Validation\Configurable;

use App\Domain\PromoCode\Validation\Configurable\MinPurchaseAmountSpec;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class MinPurchaseAmountSpecTest extends TestCase
{
    public function test_it_blocks_when_subtotal_is_less_than_minimum()
    {
        $rule = new MinPurchaseAmountSpec(50.0);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->build();
        $order = new OrderMock(30.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertFalse($result->isValid);
        $this->assertEquals('min_amount_required', $result->errorCode);
    }

    public function test_it_allows_when_subtotal_is_greater_or_equal_to_minimum()
    {
        $rule = new MinPurchaseAmountSpec(50.0);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->build();
        $order = new OrderMock(50.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}

