<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    use HasFactory;

    protected $table = 'promo_codes';

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'status',
        'starts_at',
        'expires_at',
        'max_discount_amount',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'discount_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(PromoCodeRule::class);
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(PromoCodeTier::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }

    public function restrictedUsers(): HasMany
    {
        return $this->hasMany(PromoCodeRestrictedUser::class);
    }
}
