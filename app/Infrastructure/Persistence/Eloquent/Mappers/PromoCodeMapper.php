<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\PromoCode\PromoCode as DomainPromoCode;
use App\Domain\PromoCode\PromoCodeStatus;
use App\Domain\PromoCode\ValueObjects\DiscountTier;
use App\Models\PromoCode as EloquentPromoCode;

final class PromoCodeMapper
{
    public function toDomain(EloquentPromoCode $model): DomainPromoCode
    {
        $tiers = $model->relationLoaded('tiers')
            ? $model->tiers
                ->map(fn ($tier) => new DiscountTier(
                    (int) $tier->min_completed_orders,
                    (float) $tier->discount_percent,
                ))
                ->all()
            : [];

        return new DomainPromoCode(
            code: $model->code,
            type: $model->discount_type,
            value: (float) ($model->discount_value ?? 0.0),
            status: PromoCodeStatus::from($model->status),
            validFrom: $model->starts_at?->toIso8601String(),
            validUntil: $model->expires_at?->toIso8601String(),
            maxDiscountAmount: $model->max_discount_amount !== null ? (float) $model->max_discount_amount : null,
            tiers: $tiers,
        );
    }
}
