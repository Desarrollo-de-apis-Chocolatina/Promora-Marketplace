<?php

declare(strict_types=1);

namespace App\Domain\PromoCode;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

final class PromoCodeEngine
{
    /**
     * @param  RuleSpecificationInterface[]  $specifications
     */
    public function validate(PromoCode $code, OrderableInterface $order, array $specifications): ValidationResult
    {
        foreach ($specifications as $specification) {
            $result = $specification->isSatisfiedBy($code, $order);

            if (! $result->isValid) {
                return $result;
            }
        }

        return ValidationResult::valid();
    }
}
