<?php

namespace Tests\Unit\PromoCode\Validation\Fixed;

use App\Domain\PromoCode\Validation\Fixed\CodeExistsRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeBuilder;

class CodeExistsRuleTest extends TestCase
{
    public function test_it_blocks_when_promo_code_is_null()
    {
        $rule = new CodeExistsRule;

        $result = $rule->validate(null);

        $this->assertFalse($result->isValid);
        $this->assertEquals('invalid_code', $result->errorCode);
    }

    public function test_it_allows_when_promo_code_exists()
    {
        $rule = new CodeExistsRule;
        $code = (new PromoCodeBuilder)->build();

        $result = $rule->validate($code);

        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }
}
