<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Discount;

use App\Domain\PromoCode\Contracts\DiscountStrategyInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\OrderContext;

final class DiscountCalculator
{
    public function __construct(
        private readonly DiscountStrategyInterface $fixedStrategy = new FixedDiscount,
        private readonly DiscountStrategyInterface $percentStrategy = new PercentDiscount,
        private readonly DiscountStrategyInterface $tieredStrategy = new TieredDiscount,
    ) {}

    public function calculate(PromoCode $promoCode, OrderContext $context): float
    {
        $strategy = match ($promoCode->type) {
            'fixed' => $this->fixedStrategy,
            'percent' => $this->percentStrategy,
            'tiered' => $this->tieredStrategy,
            default => throw new \InvalidArgumentException("Tipo de descuento desconocido: {$promoCode->type}"),
        };

        return $strategy->calculate($promoCode, $context);
    }
}
