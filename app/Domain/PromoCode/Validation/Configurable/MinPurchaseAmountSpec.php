<?php

namespace App\Domain\PromoCode\Validation\Configurable;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

class MinPurchaseAmountSpec implements RuleSpecificationInterface
{
    public function __construct(
        private readonly float $minAmount
    ) {
    }

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        if ($order->getSubtotal() < $this->minAmount) {
            return ValidationResult::invalid('min_amount_required');
        }

        return ValidationResult::valid();
    }
}

