<?php

namespace App\Domain\Rules\Configurable;

use App\Domain\Contracts\OrderableInterface;
use App\Domain\Contracts\RuleSpecificationInterface;
use App\Domain\Entities\PromoCode;
use App\Domain\ValueObjects\ValidationResult;

class FirstOrderOnlyRule implements RuleSpecificationInterface
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
