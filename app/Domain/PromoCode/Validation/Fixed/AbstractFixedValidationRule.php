<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Validation\Fixed;

use App\Domain\PromoCode\Contracts\FixedValidationRuleInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

abstract class AbstractFixedValidationRule implements FixedValidationRuleInterface
{
    private ?FixedValidationRuleInterface $next = null;

    public function setNext(FixedValidationRuleInterface $next): FixedValidationRuleInterface
    {
        $this->next = $next;

        return $next;
    }

    final public function validate(?PromoCode $promoCode): ValidationResult
    {
        $result = $this->check($promoCode);

        if (! $result->isValid) {
            return $result;
        }

        return $this->next?->validate($promoCode) ?? ValidationResult::valid();
    }

    abstract protected function check(?PromoCode $promoCode): ValidationResult;
}
