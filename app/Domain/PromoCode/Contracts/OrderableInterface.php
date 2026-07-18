<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\Contracts;

use App\Domain\PromoCode\ValueObjects\OrderContext;

interface OrderableInterface
{
    public function getId(): string|int;

    public function getSubtotal(): float;

    public function getOrderContext(): OrderContext;
}
