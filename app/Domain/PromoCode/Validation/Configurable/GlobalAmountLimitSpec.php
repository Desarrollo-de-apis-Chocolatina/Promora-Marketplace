<?php

namespace App\Domain\PromoCode\Validation\Configurable;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

class GlobalAmountLimitSpec implements RuleSpecificationInterface
{
    public function __construct(
        private readonly float $limit
    ) {
    }

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $amountDiscounted = $order->getOrderContext()->globalDiscountAmount;

        if ($amountDiscounted >= $this->limit) {
            return ValidationResult::invalid('maximum_discount_reached');
        }

        return ValidationResult::valid();
    }
}

