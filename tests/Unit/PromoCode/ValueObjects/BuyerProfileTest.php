<?php

declare(strict_types=1);

namespace Tests\Unit\PromoCode\ValueObjects;

use App\Domain\PromoCode\ValueObjects\BuyerProfile;
use PHPUnit\Framework\TestCase;

class BuyerProfileTest extends TestCase
{
    public function test_it_creates_a_buyer_profile_with_valid_data(): void
    {
        $profile = new BuyerProfile(
            buyerId: 10,
            completedOrdersCount: 5,
            paidPromoCodeUsages: 2,
            isFirstOrder: false
        );

        $this->assertEquals(10, $profile->buyerId);
        $this->assertEquals(5, $profile->completedOrdersCount);
        $this->assertEquals(2, $profile->paidPromoCodeUsages);
        $this->assertFalse($profile->isFirstOrder);
    }

    public function test_it_coerces_negative_values_to_zero_to_keep_domain_coherence(): void
    {
        $profile = new BuyerProfile(
            buyerId: 'user_123',
            completedOrdersCount: -3,
            paidPromoCodeUsages: -1,
            isFirstOrder: true
        );

        $this->assertEquals('user_123', $profile->buyerId);
        $this->assertEquals(0, $profile->completedOrdersCount);
        $this->assertEquals(0, $profile->paidPromoCodeUsages);
        $this->assertTrue($profile->isFirstOrder);
    }

    public function test_it_uses_default_values_when_not_provided(): void
    {
        $profile = new BuyerProfile(buyerId: 99);

        $this->assertEquals(99, $profile->buyerId);
        $this->assertEquals(0, $profile->completedOrdersCount);
        $this->assertEquals(0, $profile->paidPromoCodeUsages);
        $this->assertFalse($profile->isFirstOrder);
    }
}
