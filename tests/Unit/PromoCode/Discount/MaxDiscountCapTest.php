<?php

declare(strict_types=1);

namespace Tests\Unit\PromoCode\Discount;

use App\Domain\PromoCode\Discount\MaxDiscountCap;
use PHPUnit\Framework\TestCase;

class MaxDiscountCapTest extends TestCase
{
    public function test_it_caps_the_discount_when_it_exceeds_the_limit(): void
    {
        $cap = new MaxDiscountCap;

        $result = $cap->apply(40.0, 25.0);

        $this->assertEquals(25.0, $result);
    }

    public function test_it_leaves_the_discount_untouched_when_below_the_limit(): void
    {
        $cap = new MaxDiscountCap;

        $result = $cap->apply(10.0, 25.0);

        $this->assertEquals(10.0, $result);
    }

    public function test_it_leaves_the_discount_untouched_when_there_is_no_limit(): void
    {
        $cap = new MaxDiscountCap;

        $result = $cap->apply(999.0, null);

        $this->assertEquals(999.0, $result);
    }
}
