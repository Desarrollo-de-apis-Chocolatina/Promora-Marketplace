<?php

namespace Tests\Factories;

use App\Domain\Contracts\OrderableInterface;
use App\Domain\ValueObjects\OrderContext;

class OrderMock implements OrderableInterface
{
    public function __construct(
        private float $subtotal,
        private OrderContext $context
    ) {
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
