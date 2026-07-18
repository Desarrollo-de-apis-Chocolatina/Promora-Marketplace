<?php

namespace App\Domain\Contracts;

use App\Domain\ValueObjects\OrderContext;

interface OrderableInterface
{
    public function getSubtotal(): float;
    public function getOrderContext(): OrderContext;
}
