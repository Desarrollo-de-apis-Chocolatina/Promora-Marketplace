<?php

namespace Tests\Unit\Rules\Configurable;

use App\Domain\Rules\Configurable\GlobalUsageLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class GlobalUsageLimitRuleTest extends TestCase
{
    public function test_it_blocks_when_global_usage_limit_is_reached()
    {
        $rule = new GlobalUsageLimitRule(100);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->withGlobalPaidUsages(100)->build();
        $order = new OrderMock(50.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertFalse($result->isValid);
        $this->assertEquals('usage_limit_reached', $result->errorCode);
    }

    public function test_it_allows_when_global_usage_limit_is_not_reached()
    {
        $rule = new GlobalUsageLimitRule(100);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->withGlobalPaidUsages(99)->build();
        $order = new OrderMock(50.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
