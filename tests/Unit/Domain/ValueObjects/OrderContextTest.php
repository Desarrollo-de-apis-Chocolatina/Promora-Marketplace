<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\BuyerProfile;
use App\Domain\ValueObjects\OrderContext;
use PHPUnit\Framework\TestCase;

class OrderContextTest extends TestCase
{
    public function test_it_creates_an_order_context_with_valid_data(): void
    {
        $buyerProfile = new BuyerProfile(buyerId: 1, completedOrdersCount: 2);
        
        $context = new OrderContext(
            orderId: 'ORD-001',
            subtotal: 150.50,
            categoryId: 'cat_10',
            categoryAncestors: ['cat_1', 'cat_5'],
            buyerProfile: $buyerProfile,
            paidPromoCodeUsages: 1,
            globalPaidUsages: 100,
            globalDiscountAmount: 500.0,
            currentOrders: ['ORD-999', 'ORD-888']
        );

        $this->assertEquals('ORD-001', $context->orderId);
        $this->assertEquals(150.50, $context->subtotal);
        $this->assertEquals('cat_10', $context->categoryId);
        $this->assertEquals(['cat_1', 'cat_5'], $context->categoryAncestors);
        $this->assertSame($buyerProfile, $context->buyerProfile);
        $this->assertEquals(1, $context->paidPromoCodeUsages);
        $this->assertEquals(100, $context->globalPaidUsages);
        $this->assertEquals(500.0, $context->globalDiscountAmount);
        $this->assertEquals(['ORD-999', 'ORD-888'], $context->currentOrders);
    }

    public function test_it_coerces_negative_values_to_zero_to_keep_domain_coherence(): void
    {
        $buyerProfile = new BuyerProfile(buyerId: 2);
        
        $context = new OrderContext(
            orderId: 999,
            subtotal: -50.0,
            categoryId: 5,
            categoryAncestors: [],
            buyerProfile: $buyerProfile,
            paidPromoCodeUsages: -5,
            globalPaidUsages: -10,
            globalDiscountAmount: -200.50
        );

        $this->assertEquals(999, $context->orderId);
        $this->assertEquals(0.0, $context->subtotal);
        $this->assertEquals(5, $context->categoryId);
        $this->assertEquals([], $context->categoryAncestors);
        $this->assertEquals(0, $context->paidPromoCodeUsages);
        $this->assertEquals(0, $context->globalPaidUsages);
        $this->assertEquals(0.0, $context->globalDiscountAmount);
        $this->assertEquals([], $context->currentOrders);
    }

    public function test_it_uses_default_values_when_not_provided(): void
    {
        $buyerProfile = new BuyerProfile(buyerId: 3);
        
        $context = new OrderContext(
            orderId: 'ORD-002',
            subtotal: 100.0,
            categoryId: 'cat_2',
            categoryAncestors: ['cat_1'],
            buyerProfile: $buyerProfile
        );

        $this->assertEquals('ORD-002', $context->orderId);
        $this->assertEquals(100.0, $context->subtotal);
        $this->assertEquals('cat_2', $context->categoryId);
        $this->assertEquals(['cat_1'], $context->categoryAncestors);
        $this->assertEquals(0, $context->paidPromoCodeUsages);
        $this->assertEquals(0, $context->globalPaidUsages);
        $this->assertEquals(0.0, $context->globalDiscountAmount);
        $this->assertEquals([], $context->currentOrders);
    }
}
