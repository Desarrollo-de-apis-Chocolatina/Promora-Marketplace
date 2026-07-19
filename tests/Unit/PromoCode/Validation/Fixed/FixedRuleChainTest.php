<?php

namespace Tests\Unit\PromoCode\Validation\Fixed;

use App\Domain\PromoCode\PromoCodeStatus;
use App\Domain\PromoCode\Validation\Fixed\CodeExistsRule;
use App\Domain\PromoCode\Validation\Fixed\FixedRuleChain;
use App\Domain\PromoCode\Validation\Fixed\IsActiveRule;
use App\Domain\PromoCode\Validation\Fixed\WithinValidityPeriodRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeBuilder;

class FixedRuleChainTest extends TestCase
{
    private function makeChain(\DateTimeImmutable $now): FixedRuleChain
    {
        return new FixedRuleChain(
            new CodeExistsRule(),
            new WithinValidityPeriodRule($now),
            new IsActiveRule()
        );
    }

    public function test_it_blocks_when_code_does_not_exist()
    {
        $chain = $this->makeChain(new \DateTimeImmutable('2026-07-18'));

        $result = $chain->validate(null);

        $this->assertFalse($result->isValid);
        $this->assertEquals('invalid_code', $result->errorCode);
    }

    public function test_it_blocks_when_code_is_expired_even_if_status_is_active()
    {
        $chain = $this->makeChain(new \DateTimeImmutable('2026-07-18'));
        $code = (new PromoCodeBuilder())
            ->withStatus(PromoCodeStatus::ACTIVE)
            ->withValidUntil('2026-07-01')
            ->build();

        $result = $chain->validate($code);

        $this->assertFalse($result->isValid);
        $this->assertEquals('expired_coupon', $result->errorCode);
    }

    public function test_it_blocks_when_code_is_paused_even_if_within_validity_period()
    {
        $chain = $this->makeChain(new \DateTimeImmutable('2026-07-18'));
        $code = (new PromoCodeBuilder())->withStatus(PromoCodeStatus::PAUSED)->build();

        $result = $chain->validate($code);

        $this->assertFalse($result->isValid);
        $this->assertEquals('invalid_code', $result->errorCode);
    }

    public function test_it_allows_when_code_exists_is_within_validity_and_is_active()
    {
        $chain = $this->makeChain(new \DateTimeImmutable('2026-07-18'));
        $code = (new PromoCodeBuilder())
            ->withStatus(PromoCodeStatus::ACTIVE)
            ->withValidFrom('2026-07-01')
            ->withValidUntil('2026-08-01')
            ->build();

        $result = $chain->validate($code);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }

    public function test_it_stops_at_the_first_failure_and_does_not_evaluate_later_rules()
    {
        // Código inexistente: nunca debería intentar leer ->status ni ->validFrom.
        $chain = $this->makeChain(new \DateTimeImmutable('2026-07-18'));

        $result = $chain->validate(null);

        $this->assertEquals('invalid_code', $result->errorCode);
    }
}
