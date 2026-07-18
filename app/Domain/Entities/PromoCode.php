<?php

namespace App\Domain\Entities;

use App\Domain\Enums\PromoCodeStatus;

class PromoCode
{
    public function __construct(
        public readonly string $code,
        public readonly string $type,
        public readonly float $value,
        public readonly PromoCodeStatus $status,
        public readonly ?string $validFrom = null,
        public readonly ?string $validUntil = null
    ) {
    }
}
