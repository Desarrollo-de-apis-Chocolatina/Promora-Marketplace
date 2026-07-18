<?php

namespace App\Domain\Rules\Configurable;

use App\Domain\Contracts\OrderableInterface;
use App\Domain\Contracts\PromoCodeRepositoryInterface;
use App\Domain\Contracts\RuleSpecificationInterface;
use App\Domain\Entities\PromoCode;
use App\Domain\ValueObjects\ValidationResult;

class GlobalUsageLimitRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly int $limit,
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $excludeOrderIds = $order->getOrderContext()->currentOrders;

        $count = $this->repository->countGlobalUsages($code->code, $excludeOrderIds);

        if ($count >= $this->limit) {
            return ValidationResult::invalid('usage_limit_reached');
        }

        return ValidationResult::valid();
    }
}
