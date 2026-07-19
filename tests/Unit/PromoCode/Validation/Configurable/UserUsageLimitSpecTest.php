<?php

namespace Tests\Unit\PromoCode\Validation\Configurable;

use App\Domain\PromoCode\Validation\Configurable\UserUsageLimitSpec;
use PHPUnit\Framework\TestCase;
use Tests\Factories\BuyerProfileBuilder;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class UserUsageLimitSpecTest extends TestCase
{
    public function test_it_blocks_when_user_usage_limit_is_reached()
    {
        $rule = new UserUsageLimitSpec(3);
        $code = (new PromoCodeBuilder)->build();

        $buyer = (new BuyerProfileBuilder)->withId(10)->withPaidPromoCodeUsages(3)->build();
        $context = (new OrderContextBuilder)->withBuyerProfile($buyer)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertFalse($result->isValid);
        $this->assertEquals('usage_limit_reached', $result->errorCode);
    }

    public function test_it_allows_when_user_usage_limit_is_not_reached()
    {
        $rule = new UserUsageLimitSpec(3);
        $code = (new PromoCodeBuilder)->build();

        $buyer = (new BuyerProfileBuilder)->withId(10)->withPaidPromoCodeUsages(2)->build();
        $context = (new OrderContextBuilder)->withBuyerProfile($buyer)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
