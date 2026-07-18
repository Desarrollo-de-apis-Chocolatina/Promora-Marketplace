<?php

namespace App\Domain\PromoCode\Validation\Configurable;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

class FirstOrderOnlySpec implements RuleSpecificationInterface
{
    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $buyerProfile = $order->getOrderContext()->buyerProfile;

        if (!$buyerProfile->isFirstOrder) {
            return ValidationResult::invalid('code_already_used');
        }

        return ValidationResult::valid();
    }
}

