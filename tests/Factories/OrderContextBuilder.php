<?php

namespace Tests\Factories;

use App\Domain\ValueObjects\BuyerProfile;
use App\Domain\ValueObjects\OrderContext;

class OrderContextBuilder
{
    private BuyerProfile $buyerProfile;
    private ?int $categoryId = null;
    private array $currentOrders = [];

    public function __construct()
    {
        $this->buyerProfile = (new BuyerProfileBuilder())->build();
    }

    public function withBuyerProfile(BuyerProfile $buyerProfile): self
    {
        $this->buyerProfile = $buyerProfile;
        return $this;
    }

    public function withCategory(int $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function withCurrentOrders(array $currentOrders): self
    {
        $this->currentOrders = $currentOrders;
        return $this;
    }

    public function build(): OrderContext
    {
        return new OrderContext(
            $this->buyerProfile,
            $this->categoryId,
            $this->currentOrders
        );
    }
}
