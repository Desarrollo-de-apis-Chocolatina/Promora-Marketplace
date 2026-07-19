<?php

namespace App\Domain\PromoCode\Validation\Configurable;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

class UserUsageLimitSpec implements RuleSpecificationInterface
{
    public function __construct(
        private readonly int $limit
    ) {}

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $count = $order->getOrderContext()->buyerProfile->paidPromoCodeUsages;

        if ($count >= $this->limit) {
            return ValidationResult::invalid('usage_limit_reached');
        }

        return ValidationResult::valid();
    }
}
