<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\ValueObjects;

use App\Domain\PromoCode\Contracts\OrderableInterface;

final readonly class OrderContext implements OrderableInterface
{
    public string|int $orderId;

    public float $subtotal;

    public string|int $categoryId;

    /**
     * @var array<string|int>
     */
    public array $categoryAncestors;

    public BuyerProfile $buyerProfile;

    public int $paidPromoCodeUsages;

    public int $globalPaidUsages;

    public float $globalDiscountAmount;

    /**
     * @var array<mixed>
     */
    public array $currentOrders;

    /**
     * @param  array<string|int>  $categoryAncestors
     * @param  array<mixed>  $currentOrders
     */
    public function __construct(
        string|int $orderId,
        float $subtotal,
        string|int $categoryId,
        array $categoryAncestors,
        BuyerProfile $buyerProfile,
        int $paidPromoCodeUsages = 0,
        int $globalPaidUsages = 0,
        float $globalDiscountAmount = 0.0,
        array $currentOrders = []
    ) {
        $this->orderId = $orderId;
        $this->currentOrders = $currentOrders;
        // Evitamos excepciones técnicas y mantenemos la coherencia del dominio
        $this->subtotal = max(0.0, $subtotal);
        $this->categoryId = $categoryId;
        $this->categoryAncestors = $categoryAncestors;
        $this->buyerProfile = $buyerProfile;
        $this->paidPromoCodeUsages = max(0, $paidPromoCodeUsages);
        $this->globalPaidUsages = max(0, $globalPaidUsages);
        $this->globalDiscountAmount = max(0.0, $globalDiscountAmount);
    }

    public function getId(): string|int
    {
        return $this->orderId;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getOrderContext(): OrderContext
    {
        return $this;
    }
}
