<?php

declare(strict_types=1);

namespace App\Domain\PromoCode;

final class PromoCode
{
    /**
     * @param  ValueObjects\DiscountTier[]  $tiers
     */
    public function __construct(
        public readonly string $code,
        public readonly string $type,
        public readonly float $value,
        public readonly PromoCodeStatus $status,
        public readonly ?string $validFrom = null,
        public readonly ?string $validUntil = null,
        public readonly ?float $maxDiscountAmount = null,
        public readonly array $tiers = [],
    ) {}
}
