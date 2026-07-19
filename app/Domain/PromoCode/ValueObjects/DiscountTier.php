<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\ValueObjects;

final readonly class DiscountTier
{
    public function __construct(
        public int $minCompletedOrders,
        public float $discountPercent,
    ) {}
}
