<?php

namespace Tests\Factories;

use App\Domain\ValueObjects\BuyerProfile;

class BuyerProfileBuilder
{
    private string|int $buyerId = 1;
    private int $completedOrdersCount = 0;
    private int $paidPromoCodeUsages = 0;
    private bool $isFirstOrder = false;

    public function withId(string|int $buyerId): self
    {
        $this->buyerId = $buyerId;
        return $this;
    }

    public function withCompletedOrders(int $count): self
    {
        $this->completedOrdersCount = $count;
        return $this;
    }

    public function withPaidPromoCodeUsages(int $usages): self
    {
        $this->paidPromoCodeUsages = $usages;
        return $this;
    }

    public function firstOrder(bool $isFirstOrder = true): self
    {
        $this->isFirstOrder = $isFirstOrder;
        return $this;
    }

    public function build(): BuyerProfile
    {
        return new BuyerProfile(
            $this->buyerId,
            $this->completedOrdersCount,
            $this->paidPromoCodeUsages,
            $this->isFirstOrder
        );
    }
}
