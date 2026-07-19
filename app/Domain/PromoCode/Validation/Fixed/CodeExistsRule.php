<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Validation\Fixed;

use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

final class CodeExistsRule extends AbstractFixedValidationRule
{
    protected function check(?PromoCode $promoCode): ValidationResult
    {
        if ($promoCode === null) {
            return ValidationResult::invalid('invalid_code');
        }

        return ValidationResult::valid();
    }
}
