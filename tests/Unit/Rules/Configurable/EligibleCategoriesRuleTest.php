<?php

namespace Tests\Unit\Rules\Configurable;

use App\Domain\Rules\Configurable\EligibleCategoriesRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class EligibleCategoriesRuleTest extends TestCase
{
    public function test_it_blocks_when_category_is_not_eligible()
    {
        $rule = new EligibleCategoriesRule([1, 2, 3]);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->withCategory(4)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertFalse($result->isValid);
        $this->assertEquals('invalid_code', $result->errorCode);
    }

    public function test_it_allows_when_category_is_eligible()
    {
        $rule = new EligibleCategoriesRule([1, 2, 3]);
        $code = (new PromoCodeBuilder())->build();
        $context = (new OrderContextBuilder())->withCategory(2)->build();
        $order = new OrderMock(100.0, $context);

        $result = $rule->isSatisfiedBy($code, $order);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
