<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Validation\Fixed;

use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

final class WithinValidityPeriodRule extends AbstractFixedValidationRule
{
    public function __construct(
        private readonly \DateTimeImmutable $now = new \DateTimeImmutable()
    ) {
    }

    protected function check(?PromoCode $promoCode): ValidationResult
    {
        // La cadena garantiza que CodeExistsRule ya corrió antes, así que $promoCode no es null aquí.
        if ($promoCode->validFrom !== null && $this->now < new \DateTimeImmutable($promoCode->validFrom)) {
            return ValidationResult::invalid('expired_coupon');
        }

        if ($promoCode->validUntil !== null && $this->now > new \DateTimeImmutable($promoCode->validUntil)) {
            return ValidationResult::invalid('expired_coupon');
        }

        return ValidationResult::valid();
    }
}
