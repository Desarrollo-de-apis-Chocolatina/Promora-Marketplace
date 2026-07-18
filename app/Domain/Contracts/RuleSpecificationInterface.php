<?php

namespace App\Domain\Contracts;

use App\Domain\Entities\PromoCode;
use App\Domain\ValueObjects\ValidationResult;

interface RuleSpecificationInterface
{
    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult;
}
