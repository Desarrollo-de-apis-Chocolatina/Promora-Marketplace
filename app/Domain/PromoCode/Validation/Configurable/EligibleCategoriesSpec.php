<?php

namespace App\Domain\PromoCode\Validation\Configurable;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

class EligibleCategoriesSpec implements RuleSpecificationInterface
{
    /**
     * @param  array<int|string>  $eligibleCategoryIds
     */
    public function __construct(
        private readonly array $eligibleCategoryIds
    ) {}

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $context = $order->getOrderContext();
        $categoryId = $context->categoryId;
        $ancestors = $context->categoryAncestors;

        $allOrderCategories = array_merge([$categoryId], $ancestors);
        $isEligible = false;

        foreach ($allOrderCategories as $catId) {
            if (in_array($catId, $this->eligibleCategoryIds, true) || in_array((string) $catId, $this->eligibleCategoryIds, true) || in_array((int) $catId, $this->eligibleCategoryIds, true)) {
                $isEligible = true;
                break;
            }
        }

        if (! $isEligible) {
            return ValidationResult::invalid('invalid_code');
        }

        return ValidationResult::valid();
    }
}
