<?php

namespace App\Domain\ValueObjects;

readonly class OrderContext
{
    /**
     * @param BuyerProfile $buyerProfile
     * @param int|null $categoryId
     * @param array $currentOrders 
     */
    public function __construct(
        public BuyerProfile $buyerProfile,
        public ?int $categoryId = null,
        public array $currentOrders = []
    ) {
    }
}
