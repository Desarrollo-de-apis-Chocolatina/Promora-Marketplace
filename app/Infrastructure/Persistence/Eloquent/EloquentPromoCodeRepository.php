<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\PromoCode\Contracts\PromoCodeRepositoryInterface;
use App\Domain\PromoCode\PromoCode as DomainPromoCode;
use App\Domain\PromoCode\PromoCodeStatus;
use App\Domain\PromoCode\ValueObjects\BuyerProfile;
use App\Domain\PromoCode\ValueObjects\OrderContext;
use App\Infrastructure\Persistence\Eloquent\Mappers\PromoCodeMapper;
use App\Models\Order;
use App\Models\PromoCode as EloquentPromoCode;
use App\Models\PromoCodeUsage;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;

final class EloquentPromoCodeRepository implements PromoCodeRepositoryInterface
{
    public function __construct(
        private readonly PromoCodeMapper $mapper = new PromoCodeMapper,
    ) {}

    public function findByCode(string $code): ?DomainPromoCode
    {
        $model = EloquentPromoCode::query()->with('tiers')->where('code', $code)->first();

        return $model !== null ? $this->mapper->toDomain($model) : null;
    }

    public function isUserRestricted(string $code, string|int $buyerId): bool
    {
        $model = EloquentPromoCode::query()->where('code', $code)->first();

        if ($model === null) {
            return false;
        }

        return $model->restrictedUsers()->where('buyer_id', $buyerId)->exists();
    }

    public function getActiveRuleConfig(string $code): array
    {
        $model = EloquentPromoCode::query()->where('code', $code)->first();

        if ($model === null) {
            return [];
        }

        $config = [];

        foreach ($model->rules()->where('is_active', true)->get() as $rule) {
            $config[$rule->rule_type] = $rule->parameters ?? [];
        }

        return $config;
    }

    public function buildOrderContext(
        DomainPromoCode $promoCode,
        string|int $orderId,
        float $subtotal,
        string|int $categoryId,
        string|int $buyerId,
        array $currentOrders = [],
    ): OrderContext {
        $model = EloquentPromoCode::query()->where('code', $promoCode->code)->first();
        $promoCodeId = $model?->id;

        $completedOrdersCount = Order::query()
            ->where('buyer_id', $buyerId)
            ->where('order_status', 'completed')
            ->when(!empty($currentOrders), fn ($q) => $q->whereNotIn('id', $currentOrders))
            ->count();

        $isFirstOrder = ! Order::query()
            ->where('buyer_id', $buyerId)
            ->when(!empty($currentOrders), fn ($q) => $q->whereNotIn('id', $currentOrders))
            ->exists();

        $paidPromoCodeUsages = $promoCodeId !== null
            ? PromoCodeUsage::query()
                ->where('promo_code_id', $promoCodeId)
                ->where('buyer_id', $buyerId)
                ->where('payment_status', 'paid')
                ->count()
            : 0;

        $globalPaidUsages = $promoCodeId !== null
            ? PromoCodeUsage::query()
                ->where('promo_code_id', $promoCodeId)
                ->where('payment_status', 'paid')
                ->count()
            : 0;

        $globalDiscountAmount = $promoCodeId !== null
            ? (float) PromoCodeUsage::query()
                ->where('promo_code_id', $promoCodeId)
                ->where('payment_status', 'paid')
                ->sum('discount_amount')
            : 0.0;

        $buyerProfile = new BuyerProfile(
            buyerId: $buyerId,
            completedOrdersCount: $completedOrdersCount,
            paidPromoCodeUsages: $paidPromoCodeUsages,
            isFirstOrder: $isFirstOrder,
        );

        return new OrderContext(
            orderId: $orderId,
            subtotal: $subtotal,
            categoryId: $categoryId,
            categoryAncestors: $this->resolveCategoryAncestors($categoryId),
            buyerProfile: $buyerProfile,
            paidPromoCodeUsages: $paidPromoCodeUsages,
            globalPaidUsages: $globalPaidUsages,
            globalDiscountAmount: $globalDiscountAmount,
        );
    }

    public function create(array $data): DomainPromoCode
    {
        return DB::transaction(function () use ($data) {
            $model = EloquentPromoCode::create([
                'code' => $data['code'],
                'discount_type' => $data['discount_type'],
                'discount_value' => $data['discount_value'] ?? null,
                'status' => $data['status'] ?? PromoCodeStatus::DRAFT->value,
                'starts_at' => $data['starts_at'],
                'expires_at' => $data['expires_at'],
                'max_discount_amount' => $data['max_discount_amount'] ?? null,
            ]);

            foreach ($data['rules'] ?? [] as $rule) {
                $model->rules()->create([
                    'rule_type' => $rule['rule_type'],
                    'parameters' => $rule['parameters'] ?? [],
                    'is_active' => $rule['is_active'] ?? true,
                ]);
            }

            foreach ($data['tiers'] ?? [] as $tier) {
                $model->tiers()->create([
                    'min_completed_orders' => $tier['min_completed_orders'],
                    'discount_percent' => $tier['discount_percent'],
                ]);
            }

            foreach ($data['restricted_user_ids'] ?? [] as $buyerId) {
                $model->restrictedUsers()->create(['buyer_id' => $buyerId]);
            }

            return $this->mapper->toDomain($model->load('tiers'));
        });
    }

    /**
     * @return array<int>
     */
    private function resolveCategoryAncestors(string|int $categoryId): array
    {
        $ancestors = [];
        $category = ServiceCategory::query()->find($categoryId);

        while ($category !== null && $category->parent_id !== null) {
            $ancestors[] = $category->parent_id;
            $category = ServiceCategory::query()->find($category->parent_id);
        }

        return $ancestors;
    }
}
