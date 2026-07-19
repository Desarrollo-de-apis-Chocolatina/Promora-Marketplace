<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Contracts;

use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

interface RuleSpecificationInterface
{
    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult;
}
