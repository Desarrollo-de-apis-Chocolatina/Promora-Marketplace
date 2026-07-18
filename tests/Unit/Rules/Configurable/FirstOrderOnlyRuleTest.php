<?php

namespace Tests\Unit\Rules\Configurable;

use App\Domain\Rules\Configurable\FirstOrderOnlyRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\BuyerProfileBuilder;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class FirstOrderOnlyRuleTest extends TestCase
{
    public function test_it_blocks_when_it_is_not_first_order()
    {
        $rule = new FirstOrderOnlyRule();
        $code = (new PromoCodeBuilder())->build();
        $buyer = (new BuyerProfileBuilder())->firstOrder(false)->build();
        $context = (new OrderContextBuilder())->withBuyerProfile($buyer)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertFalse($result->isValid);
        $this->assertEquals('code_already_used', $result->errorCode);
    }

    public function test_it_allows_when_it_is_first_order()
    {
        $rule = new FirstOrderOnlyRule();
        $code = (new PromoCodeBuilder())->build();
        $buyer = (new BuyerProfileBuilder())->firstOrder(true)->build();
        $context = (new OrderContextBuilder())->withBuyerProfile($buyer)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
