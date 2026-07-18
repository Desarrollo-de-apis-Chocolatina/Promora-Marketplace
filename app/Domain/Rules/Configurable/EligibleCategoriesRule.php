<?php

namespace App\Domain\Rules\Configurable;

use App\Domain\Contracts\OrderableInterface;
use App\Domain\Contracts\RuleSpecificationInterface;
use App\Domain\Entities\PromoCode;
use App\Domain\ValueObjects\ValidationResult;

class EligibleCategoriesRule implements RuleSpecificationInterface
{
    /**
     * @param array<int> $eligibleCategoryIds
     */
    public function __construct(
        private readonly array $eligibleCategoryIds
    ) {
    }

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $categoryId = $order->getOrderContext()->categoryId;

        if ($categoryId === null || !in_array($categoryId, $this->eligibleCategoryIds, true)) {
            return ValidationResult::invalid('invalid_code');
        }

        return ValidationResult::valid();
    }
}
