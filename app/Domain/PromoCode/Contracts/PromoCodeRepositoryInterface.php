<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Contracts;

use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\ValueObjects\OrderContext;

interface PromoCodeRepositoryInterface
{
    public function findByCode(string $code): ?PromoCode;

    public function isUserRestricted(string $code, string|int $buyerId): bool;

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getActiveRuleConfig(string $code): array;

    public function buildOrderContext(
        PromoCode $promoCode,
        string|int $orderId,
        float $subtotal,
        string|int $categoryId,
        string|int $buyerId,
        array $currentOrders = [],
    ): OrderContext;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): PromoCode;
}
