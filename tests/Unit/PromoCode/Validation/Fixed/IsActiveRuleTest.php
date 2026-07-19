<?php

namespace Tests\Unit\PromoCode\Validation\Fixed;

use App\Domain\PromoCode\PromoCodeStatus;
use App\Domain\PromoCode\Validation\Fixed\IsActiveRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeBuilder;

class IsActiveRuleTest extends TestCase
{
    public function test_it_blocks_when_promo_code_is_paused()
    {
        $rule = new IsActiveRule;
        $code = (new PromoCodeBuilder)->withStatus(PromoCodeStatus::PAUSED)->build();

        $result = $rule->validate($code);

        $this->assertFalse($result->isValid);
        $this->assertEquals('invalid_code', $result->errorCode);
    }

    public function test_it_blocks_when_promo_code_is_draft()
    {
        $rule = new IsActiveRule;
        $code = (new PromoCodeBuilder)->withStatus(PromoCodeStatus::DRAFT)->build();

        $result = $rule->validate($code);

        $this->assertFalse($result->isValid);
        $this->assertEquals('invalid_code', $result->errorCode);
    }

    public function test_it_allows_when_promo_code_is_active()
    {
        $rule = new IsActiveRule;
        $code = (new PromoCodeBuilder)->withStatus(PromoCodeStatus::ACTIVE)->build();

        $result = $rule->validate($code);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
