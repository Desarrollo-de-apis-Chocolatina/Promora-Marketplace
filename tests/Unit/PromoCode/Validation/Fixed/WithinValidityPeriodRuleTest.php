<?php

namespace Tests\Unit\PromoCode\Validation\Fixed;

use App\Domain\PromoCode\Validation\Fixed\WithinValidityPeriodRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeBuilder;

class WithinValidityPeriodRuleTest extends TestCase
{
    public function test_it_blocks_when_promo_code_has_not_started_yet()
    {
        $now = new \DateTimeImmutable('2026-07-18');
        $rule = new WithinValidityPeriodRule($now);
        $code = (new PromoCodeBuilder())->withValidFrom('2026-08-01')->build();

        $result = $rule->validate($code);

        $this->assertFalse($result->isValid);
        $this->assertEquals('expired_coupon', $result->errorCode);
    }

    public function test_it_blocks_when_promo_code_already_expired()
    {
        $now = new \DateTimeImmutable('2026-07-18');
        $rule = new WithinValidityPeriodRule($now);
        $code = (new PromoCodeBuilder())->withValidUntil('2026-07-01')->build();

        $result = $rule->validate($code);

        $this->assertFalse($result->isValid);
        $this->assertEquals('expired_coupon', $result->errorCode);
    }

    public function test_it_allows_when_within_validity_period()
    {
        $now = new \DateTimeImmutable('2026-07-18');
        $rule = new WithinValidityPeriodRule($now);
        $code = (new PromoCodeBuilder())
            ->withValidFrom('2026-07-01')
            ->withValidUntil('2026-08-01')
            ->build();

        $result = $rule->validate($code);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }

    public function test_it_allows_when_promo_code_has_no_date_bounds()
    {
        $rule = new WithinValidityPeriodRule(new \DateTimeImmutable());
        $code = (new PromoCodeBuilder())->build();

        $result = $rule->validate($code);

        $this->assertTrue($result->isValid);
    }
}
