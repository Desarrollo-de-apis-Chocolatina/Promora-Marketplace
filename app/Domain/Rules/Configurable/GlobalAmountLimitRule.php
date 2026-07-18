<?php

namespace App\Domain\Rules\Configurable;

use App\Domain\Contracts\OrderableInterface;
use App\Domain\Contracts\RuleSpecificationInterface;
use App\Domain\Entities\PromoCode;
use App\Domain\ValueObjects\ValidationResult;

class GlobalAmountLimitRule implements RuleSpecificationInterface
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
