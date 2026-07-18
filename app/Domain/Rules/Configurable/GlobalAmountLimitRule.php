<?php

namespace App\Domain\Rules\Configurable;

use App\Domain\Contracts\OrderableInterface;
use App\Domain\Contracts\PromoCodeRepositoryInterface;
use App\Domain\Contracts\RuleSpecificationInterface;
use App\Domain\Entities\PromoCode;
use App\Domain\ValueObjects\ValidationResult;

class GlobalAmountLimitRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly float $limit,
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $excludeOrderIds = $order->getOrderContext()->currentOrders;

        $amountDiscounted = $this->repository->getGlobalAmountDiscounted($code->code, $excludeOrderIds);

        if ($amountDiscounted >= $this->limit) {
            return ValidationResult::invalid('maximum_discount_reached');
        }

        return ValidationResult::valid();
    }
}
