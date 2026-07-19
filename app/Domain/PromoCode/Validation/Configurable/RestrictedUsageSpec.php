<?php

namespace App\Domain\PromoCode\Validation\Configurable;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\PromoCodeRepositoryInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\ValidationResult;

class RestrictedUsageSpec implements RuleSpecificationInterface
{
    public function __construct(
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(PromoCode $code, OrderableInterface $order): ValidationResult
    {
        $userId = $order->getOrderContext()->buyerProfile->buyerId;

        $isRestricted = $this->repository->isUserRestricted($code->code, $userId);

        if (!$isRestricted) {
            return ValidationResult::invalid('restricted_usage');
        }

        return ValidationResult::valid();
    }
}

