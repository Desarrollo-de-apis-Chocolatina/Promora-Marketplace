<?php

namespace Tests\Unit\PromoCode;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\PromoCodeEngine;
use App\Domain\PromoCode\ValueObjects\ValidationResult;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextBuilder;
use Tests\Factories\OrderMock;
use Tests\Factories\PromoCodeBuilder;

class PromoCodeEngineTest extends TestCase
{
    private function makeSpec(bool $satisfied, string $errorCode = 'some_error'): RuleSpecificationInterface
    {
        return new class($satisfied, $errorCode) implements RuleSpecificationInterface
        {
            public function __construct(
                private readonly bool $satisfied,
                private readonly string $errorCode
            ) {}

            public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
            {
                return $this->satisfied
                    ? ValidationResult::valid()
                    : ValidationResult::invalid($this->errorCode);
            }
        };
    }

    public function test_it_is_valid_when_there_are_no_specifications()
    {
        $engine = new PromoCodeEngine;
        $code = (new PromoCodeBuilder)->build();
        $order = new OrderMock(100.0, (new OrderContextBuilder)->build());

        $result = $engine->validate($code, $order, []);

        $this->assertTrue($result->isValid);
    }

    public function test_it_is_valid_when_every_specification_is_satisfied()
    {
        $engine = new PromoCodeEngine;
        $code = (new PromoCodeBuilder)->build();
        $order = new OrderMock(100.0, (new OrderContextBuilder)->build());

        $result = $engine->validate($code, $order, [
            $this->makeSpec(true),
            $this->makeSpec(true),
        ]);

        $this->assertTrue($result->isValid);
    }

    public function test_it_blocks_on_the_first_unsatisfied_specification()
    {
        $engine = new PromoCodeEngine;
        $code = (new PromoCodeBuilder)->build();
        $order = new OrderMock(100.0, (new OrderContextBuilder)->build());

        $result = $engine->validate($code, $order, [
            $this->makeSpec(true),
            $this->makeSpec(false, 'min_amount_required'),
            $this->makeSpec(false, 'this_should_never_run'),
        ]);

        $this->assertFalse($result->isValid);
        $this->assertEquals('min_amount_required', $result->errorCode);
    }
}
