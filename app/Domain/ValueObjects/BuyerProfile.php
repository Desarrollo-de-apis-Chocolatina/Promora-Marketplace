<?php

namespace App\Domain\ValueObjects;

readonly class BuyerProfile
{
    public function __construct(
        public int $id,
        public bool $isFirstOrder = false
    ) {
    }
}
