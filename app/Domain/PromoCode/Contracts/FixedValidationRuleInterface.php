<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Contracts;

use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

interface FixedValidationRuleInterface
{
    public function validate(?PromoCode $promoCode): ValidationResult;

    public function setNext(FixedValidationRuleInterface $next): FixedValidationRuleInterface;
}
