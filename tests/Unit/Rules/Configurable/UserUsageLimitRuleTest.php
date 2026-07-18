<?php

namespace Tests\Unit\Rules\Configurable;

use App\Domain\Contracts\PromoCodeRepositoryInterface;
use App\Domain\Rules\Configurable\UserUsageLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\BuyerProfileBuilder;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class UserUsageLimitRuleTest extends TestCase
{
    public function test_it_blocks_when_user_usage_limit_is_reached()
    {
        $repository = $this->createMock(PromoCodeRepositoryInterface::class);
        $repository->method('countUserUsages')->willReturn(3);

        $rule = new UserUsageLimitRule(3, $repository);
        $code = (new PromoCodeBuilder())->build();
        
        $buyer = (new BuyerProfileBuilder())->withId(10)->build();
        $context = (new OrderContextBuilder())->withBuyerProfile($buyer)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertFalse($result->isValid);
        $this->assertEquals('usage_limit_reached', $result->errorCode);
    }

    public function test_it_allows_when_user_usage_limit_is_not_reached()
    {
        $repository = $this->createMock(PromoCodeRepositoryInterface::class);
        $repository->method('countUserUsages')->willReturn(2);

        $rule = new UserUsageLimitRule(3, $repository);
        $code = (new PromoCodeBuilder())->build();
        
        $buyer = (new BuyerProfileBuilder())->withId(10)->build();
        $context = (new OrderContextBuilder())->withBuyerProfile($buyer)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
