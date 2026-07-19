<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Validation\Fixed;

use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\PromoCodeStatus;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

final class IsActiveRule extends AbstractFixedValidationRule
{
    protected function check(?PromoCode $promoCode): ValidationResult
    {
        // La cadena garantiza que CodeExistsRule ya corrió antes, así que $promoCode no es null aquí.
        if ($promoCode->status !== PromoCodeStatus::ACTIVE) {
            return ValidationResult::invalid('invalid_code');
        }

        return ValidationResult::valid();
    }
}
