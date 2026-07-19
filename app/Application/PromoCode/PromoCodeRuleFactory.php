<?php

namespace App\Application\PromoCode;

use App\Domain\PromoCode\Contracts\PromoCodeRepositoryInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\Validation\Configurable\EligibleCategoriesSpec;
use App\Domain\PromoCode\Validation\Configurable\FirstOrderOnlySpec;
use App\Domain\PromoCode\Validation\Configurable\GlobalAmountLimitSpec;
use App\Domain\PromoCode\Validation\Configurable\GlobalUsageLimitSpec;
use App\Domain\PromoCode\Validation\Configurable\MinPurchaseAmountSpec;
use App\Domain\PromoCode\Validation\Configurable\RestrictedUsageSpec;
use App\Domain\PromoCode\Validation\Configurable\UserUsageLimitSpec;

class PromoCodeRuleFactory
{
    public function __construct(
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    /**
     * @param array $config
     * @return RuleSpecificationInterface[]
     */
    public function buildRules(array $config): array
    {
        $rules = [];

        foreach ($config as $ruleKey => $params) {
            $rules[] = match ($ruleKey) {
                'min_purchase_amount' => new MinPurchaseAmountSpec((float) $params['minAmount']),
                'eligible_categories' => new EligibleCategoriesSpec((array) $params['eligibleCategoryIds']),
                'first_order_only'    => new FirstOrderOnlySpec(),
                'user_usage_limit'    => new UserUsageLimitSpec((int) $params['limit']),
                'global_usage_limit'  => new GlobalUsageLimitSpec((int) $params['limit']),
                'global_amount_limit' => new GlobalAmountLimitSpec((float) $params['limit']),
                'restricted_usage'    => new RestrictedUsageSpec($this->repository),
                default => throw new \InvalidArgumentException("Regla desconocida: $ruleKey"),
            };
        }

        return $rules;
    }
}

