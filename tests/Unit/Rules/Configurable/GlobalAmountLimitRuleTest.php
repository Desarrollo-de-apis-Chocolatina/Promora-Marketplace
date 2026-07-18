<?php

namespace Tests\Unit\Rules\Configurable;

use App\Domain\Rules\Configurable\GlobalAmountLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class GlobalAmountLimitRuleTest extends TestCase
{
    public function test_it_blocks_when_global_amount_limit_is_reached()
    {
        $rule = new GlobalAmountLimitRule(1000.0);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->withGlobalDiscountAmount(1000.0)->build();
        $order = new OrderMock(50.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertFalse($result->isValid);
        $this->assertEquals('maximum_discount_reached', $result->errorCode);
    }

    public function test_it_allows_when_global_amount_limit_is_not_reached()
    {
        $rule = new GlobalAmountLimitRule(1000.0);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->withGlobalDiscountAmount(999.0)->build();
        $order = new OrderMock(50.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
