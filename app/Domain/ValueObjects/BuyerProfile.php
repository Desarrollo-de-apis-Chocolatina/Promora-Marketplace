<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final readonly class BuyerProfile
{
    public string|int $buyerId;
    public int $completedOrdersCount;
    public int $paidPromoCodeUsages;
    public bool $isFirstOrder;

    public function __construct(
        string|int $buyerId,
        int $completedOrdersCount = 0,
        int $paidPromoCodeUsages = 0,
        bool $isFirstOrder = false
    ) {
        $this->buyerId = $buyerId;
        // Se asegura la coherencia de datos sin lanzar excepciones
        $this->completedOrdersCount = max(0, $completedOrdersCount);
        $this->paidPromoCodeUsages = max(0, $paidPromoCodeUsages);
        $this->isFirstOrder = $isFirstOrder;
    }
}
