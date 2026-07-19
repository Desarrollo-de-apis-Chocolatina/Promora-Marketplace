<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Discount;

final class MaxDiscountCap
{
    public function apply(float $discount, ?float $cap): float
    {
        if ($cap === null) {
            return $discount;
        }

        return min($discount, $cap);
    }
}
