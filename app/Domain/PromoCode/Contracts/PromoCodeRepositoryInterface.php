<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Contracts;

use App\Domain\PromoCode\PromoCode;

interface PromoCodeRepositoryInterface
{
    public function findByCode(string $code): ?PromoCode;

    public function isUserRestricted(string $code, string|int $buyerId): bool;
}
