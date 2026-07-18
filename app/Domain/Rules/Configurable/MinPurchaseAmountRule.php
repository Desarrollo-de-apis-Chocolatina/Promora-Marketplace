<?php

namespace App\Domain\Rules\Configurable;

use App\Domain\Contracts\OrderableInterface;
use App\Domain\Contracts\RuleSpecificationInterface;
use App\Domain\Entities\PromoCode;
use App\Domain\ValueObjects\ValidationResult;

class MinPurchaseAmountRule implements RuleSpecificationInterface
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
