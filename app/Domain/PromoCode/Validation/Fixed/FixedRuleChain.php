<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Validation\Fixed;

use App\Domain\PromoCode\Contracts\FixedValidationRuleInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

final class FixedRuleChain
{
    private readonly FixedValidationRuleInterface $chain;

    public function __construct(
        CodeExistsRule $codeExistsRule,
        WithinValidityPeriodRule $withinValidityPeriodRule,
        IsActiveRule $isActiveRule,
    ) {
        $codeExistsRule->setNext($withinValidityPeriodRule)->setNext($isActiveRule);
        $this->chain = $codeExistsRule;
    }

    public function validate(?PromoCode $promoCode): ValidationResult
    {
        return $this->chain->validate($promoCode);
    }
}
