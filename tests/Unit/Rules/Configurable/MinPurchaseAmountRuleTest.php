<?php

namespace Tests\Unit\Rules\Configurable;

use App\Domain\Rules\Configurable\MinPurchaseAmountRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class MinPurchaseAmountRuleTest extends TestCase
{
    public function test_it_blocks_when_subtotal_is_less_than_minimum()
    {
        $rule = new MinPurchaseAmountRule(50.0);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->build();
        $order = new OrderMock(30.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertFalse($result->isValid);
        $this->assertEquals('min_amount_required', $result->errorCode);
    }

    public function test_it_allows_when_subtotal_is_greater_or_equal_to_minimum()
    {
        $rule = new MinPurchaseAmountRule(50.0);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->build();
        $order = new OrderMock(50.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
