<?php

declare(strict_types=1);

namespace App\Application\PromoCode;

use App\Domain\PromoCode\Contracts\PromoCodeRepositoryInterface;
use App\Domain\PromoCode\Discount\DiscountCalculator;
use App\Domain\PromoCode\Discount\MaxDiscountCap;
use App\Domain\PromoCode\PromoCodeEngine;
use App\Domain\PromoCode\Validation\Fixed\FixedRuleChain;

final class ValidatePromoCodeUseCase
{
    public function __construct(
        private readonly PromoCodeRepositoryInterface $repository,
        private readonly FixedRuleChain $fixedRuleChain,
        private readonly PromoCodeRuleFactory $ruleFactory,
        private readonly PromoCodeEngine $engine,
        private readonly DiscountCalculator $discountCalculator,
        private readonly MaxDiscountCap $maxDiscountCap,
    ) {}

    public function execute(
        string $code,
        string|int $orderId,
        float $subtotal,
        string|int $categoryId,
        string|int $buyerId,
        array $currentOrders = [],
    ): ValidatePromoCodeResult {
        $promoCode = $this->repository->findByCode($code);

        $fixedResult = $this->fixedRuleChain->validate($promoCode);

        if (! $fixedResult->isValid) {
            return ValidatePromoCodeResult::failure($fixedResult->errorCode, $subtotal);
        }

        $context = $this->repository->buildOrderContext($promoCode, $orderId, $subtotal, $categoryId, $buyerId, $currentOrders);

        $config = $this->repository->getActiveRuleConfig($code);
        $rules = $this->ruleFactory->buildRules($config);

        $result = $this->engine->validate($promoCode, $context, $rules);

        if (! $result->isValid) {
            return ValidatePromoCodeResult::failure($result->errorCode, $subtotal);
        }

        $discount = $this->discountCalculator->calculate($promoCode, $context);
        $finalDiscount = $this->maxDiscountCap->apply($discount, $promoCode->maxDiscountAmount);

        return ValidatePromoCodeResult::success($code, $finalDiscount, $subtotal);
    }
}
