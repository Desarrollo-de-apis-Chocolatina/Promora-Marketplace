<?php

namespace App\Domain\Contracts;

use App\Domain\Entities\PromoCode;

interface PromoCodeRepositoryInterface
{
    public function findByCode(string $code): ?PromoCode;
    public function getActiveRuleConfiguration(string $code): array;
    
    public function countUserUsages(string $code, int $userId, array $excludeOrderIds = []): int;
    public function countGlobalUsages(string $code, array $excludeOrderIds = []): int;
    public function getGlobalAmountDiscounted(string $code, array $excludeOrderIds = []): float;
    public function isUserRestricted(string $code, int $userId): bool;
    
    public function countCompletedOrders(int $userId, array $excludeOrderIds = []): int;
}
