<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Contracts;

use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\OrderContext;

interface DiscountStrategyInterface
{
    public function calculate(PromoCode $promoCode, OrderContext $context): float;
}
