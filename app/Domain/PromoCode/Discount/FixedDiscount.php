<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Discount;

use App\Domain\PromoCode\Contracts\DiscountStrategyInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\OrderContext;

final class FixedDiscount implements DiscountStrategyInterface
{
    public function calculate(PromoCode $promoCode, OrderContext $context): float
    {
        return min($promoCode->value, $context->subtotal);
    }
}
