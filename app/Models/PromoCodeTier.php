<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoCodeTier extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'promo_code_id',
        'min_completed_orders',
        'discount_percent',
    ];

    protected $casts = [
        'min_completed_orders' => 'integer',
        'discount_percent' => 'decimal:2',
    ];

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }
}
