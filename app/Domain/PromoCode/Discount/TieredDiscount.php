<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Discount;

use App\Domain\PromoCode\Contracts\DiscountStrategyInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\OrderContext;

final class TieredDiscount implements DiscountStrategyInterface
{
    public function calculate(PromoCode $promoCode, OrderContext $context): float
    {
        $completedOrders = $context->buyerProfile->completedOrdersCount;

        $tiers = $promoCode->tiers;
        usort($tiers, fn ($a, $b) => $b->minCompletedOrders <=> $a->minCompletedOrders);

        $percent = 0.0;

        foreach ($tiers as $tier) {
            if ($completedOrders >= $tier->minCompletedOrders) {
                $percent = $tier->discountPercent;
                break;
            }
        }

        return $context->subtotal * ($percent / 100);
    }
}
