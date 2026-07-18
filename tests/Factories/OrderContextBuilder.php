<?php

namespace Tests\Factories;

use App\Domain\ValueObjects\BuyerProfile;
use App\Domain\ValueObjects\OrderContext;

class OrderContextBuilder
{
    private string|int $orderId = 1;
    private float $subtotal = 0.0;
    private string|int $categoryId = 1;
    private array $categoryAncestors = [];
    private BuyerProfile $buyerProfile;
    private int $paidPromoCodeUsages = 0;
    private int $globalPaidUsages = 0;
    private float $globalDiscountAmount = 0.0;
    private array $currentOrders = [];

    public function __construct()
    {
        $this->buyerProfile = (new BuyerProfileBuilder())->build();
    }

    public function withOrderId(string|int $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function withSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    public function withBuyerProfile(BuyerProfile $buyerProfile): self
    {
        $this->buyerProfile = $buyerProfile;
        return $this;
    }

    public function withCategory(string|int $categoryId, array $ancestors = []): self
    {
        $this->categoryId = $categoryId;
        $this->categoryAncestors = $ancestors;
        return $this;
    }

    public function withCurrentOrders(array $currentOrders): self
    {
        $this->currentOrders = $currentOrders;
        return $this;
    }
    
    public function withPaidPromoCodeUsages(int $usages): self
    {
        $this->paidPromoCodeUsages = $usages;
        return $this;
    }

    public function withGlobalPaidUsages(int $usages): self
    {
        $this->globalPaidUsages = $usages;
        return $this;
    }

    public function withGlobalDiscountAmount(float $amount): self
    {
        $this->globalDiscountAmount = $amount;
        return $this;
    }

    public function build(): OrderContext
    {
        return new OrderContext(
            $this->orderId,
            $this->subtotal,
            $this->categoryId,
            $this->categoryAncestors,
            $this->buyerProfile,
            $this->paidPromoCodeUsages,
            $this->globalPaidUsages,
            $this->globalDiscountAmount,
            $this->currentOrders
        );
    }
}
