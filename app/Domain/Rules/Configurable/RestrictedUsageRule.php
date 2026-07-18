<?php

namespace App\Domain\Rules\Configurable;

use App\Domain\Contracts\OrderableInterface;
use App\Domain\Contracts\PromoCodeRepositoryInterface;
use App\Domain\Contracts\RuleSpecificationInterface;
use App\Domain\Entities\PromoCode;
use App\Domain\ValueObjects\ValidationResult;

class RestrictedUsageRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $userId = $order->getOrderContext()->buyerProfile->id;

        $isRestricted = $this->repository->isUserRestricted($code->code, $userId);

        if (!$isRestricted) {
            return ValidationResult::invalid('restricted_usage');
        }

        return ValidationResult::valid();
    }
}
