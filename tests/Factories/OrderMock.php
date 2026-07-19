<?php

namespace Tests\Factories;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\ValueObjects\OrderContext;

class OrderMock implements OrderableInterface
{
    public function __construct(
        private float $subtotal,
        private OrderContext $context,
        private string|int $id = 1
    ) {
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getOrderContext(): OrderContext
    {
        return $this->context;
    }
}
