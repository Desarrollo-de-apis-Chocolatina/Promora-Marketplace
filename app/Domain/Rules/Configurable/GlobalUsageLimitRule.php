<?php

namespace App\Domain\Rules\Configurable;

use App\Domain\Contracts\OrderableInterface;
use App\Domain\Contracts\RuleSpecificationInterface;
use App\Domain\Entities\PromoCode;
use App\Domain\ValueObjects\ValidationResult;

class GlobalUsageLimitRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly int $limit
    ) {
    }

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $count = $order->getOrderContext()->globalPaidUsages;

        if ($count >= $this->limit) {
            return ValidationResult::invalid('usage_limit_reached');
        }

        return ValidationResult::valid();
    }
}
