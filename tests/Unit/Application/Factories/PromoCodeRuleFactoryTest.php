<?php

namespace Tests\Unit\Application\Factories;

use App\Application\Factories\PromoCodeRuleFactory;
use App\Domain\Contracts\PromoCodeRepositoryInterface;
use App\Domain\Rules\Configurable\MinPurchaseAmountRule;
use App\Domain\Rules\Configurable\UserUsageLimitRule;
use PHPUnit\Framework\TestCase;

class PromoCodeRuleFactoryTest extends TestCase
{
    public function test_it_builds_correct_rules_based_on_configuration()
    {
        $repository = $this->createMock(PromoCodeRepositoryInterface::class);
        $factory = new PromoCodeRuleFactory($repository);

        $config = [
            'min_purchase_amount' => ['minAmount' => 100.0],
            'user_usage_limit' => ['limit' => 5]
        ];

        $rules = $factory->buildRules($config);

        $this->assertCount(2, $rules);
        $this->assertInstanceOf(MinPurchaseAmountRule::class, $rules[0]);
        $this->assertInstanceOf(UserUsageLimitRule::class, $rules[1]);
    }

    public function test_it_throws_exception_for_unknown_rule()
    {
        $repository = $this->createMock(PromoCodeRepositoryInterface::class);
        $factory = new PromoCodeRuleFactory($repository);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Regla desconocida: unknown_rule');

        $factory->buildRules(['unknown_rule' => []]);
    }
}
