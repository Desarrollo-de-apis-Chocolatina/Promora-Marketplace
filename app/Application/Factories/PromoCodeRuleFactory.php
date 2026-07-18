<?php

namespace App\Application\Factories;

use App\Domain\Contracts\PromoCodeRepositoryInterface;
use App\Domain\Contracts\RuleSpecificationInterface;
use App\Domain\Rules\Configurable\EligibleCategoriesRule;
use App\Domain\Rules\Configurable\FirstOrderOnlyRule;
use App\Domain\Rules\Configurable\GlobalAmountLimitRule;
use App\Domain\Rules\Configurable\GlobalUsageLimitRule;
use App\Domain\Rules\Configurable\MinPurchaseAmountRule;
use App\Domain\Rules\Configurable\RestrictedUsageRule;
use App\Domain\Rules\Configurable\UserUsageLimitRule;

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
                'min_purchase_amount' => new MinPurchaseAmountRule((float) $params['minAmount']),
                'eligible_categories' => new EligibleCategoriesRule((array) $params['eligibleCategoryIds']),
                'first_order_only'    => new FirstOrderOnlyRule(),
                'user_usage_limit'    => new UserUsageLimitRule((int) $params['limit'], $this->repository),
                'global_usage_limit'  => new GlobalUsageLimitRule((int) $params['limit'], $this->repository),
                'global_amount_limit' => new GlobalAmountLimitRule((float) $params['limit'], $this->repository),
                'restricted_usage'    => new RestrictedUsageRule($this->repository),
                default => throw new \InvalidArgumentException("Regla desconocida: $ruleKey"),
            };
        }

        return $rules;
    }
}
