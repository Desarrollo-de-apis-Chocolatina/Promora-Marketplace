<?php

namespace Tests\Unit\Rules\Configurable;

use App\Domain\Contracts\PromoCodeRepositoryInterface;
use App\Domain\Rules\Configurable\RestrictedUsageRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\BuyerProfileBuilder;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class RestrictedUsageRuleTest extends TestCase
{
    public function test_it_blocks_when_user_is_not_restricted()
    {
        $repository = $this->createMock(PromoCodeRepositoryInterface::class);
        $repository->method('isUserRestricted')->willReturn(false);

        $rule = new RestrictedUsageRule($repository);
        $code = (new PromoCodeBuilder())->build();
        $buyer = (new BuyerProfileBuilder())->withId(5)->build();
        $context = (new OrderContextBuilder())->withBuyerProfile($buyer)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertFalse($result->isValid);
        $this->assertEquals('restricted_usage', $result->errorCode);
    }

    public function test_it_allows_when_user_is_restricted()
    {
        $repository = $this->createMock(PromoCodeRepositoryInterface::class);
        $repository->method('isUserRestricted')->willReturn(true);

        $rule = new RestrictedUsageRule($repository);
        $code = (new PromoCodeBuilder())->build();
        $buyer = (new BuyerProfileBuilder())->withId(5)->build();
        $context = (new OrderContextBuilder())->withBuyerProfile($buyer)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
