<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePromoCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:promo_codes,code'],
            'discount_type' => ['required', Rule::in(['fixed', 'percent', 'tiered'])],
            'discount_value' => ['required_unless:discount_type,tiered', 'nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in(['draft', 'active', 'paused', 'expired'])],
            'starts_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after:starts_at'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],

            'rules' => ['sometimes', 'array'],
            'rules.*.rule_type' => ['required_with:rules', Rule::in([
                'min_purchase_amount',
                'eligible_categories',
                'first_order_only',
                'user_usage_limit',
                'global_usage_limit',
                'global_amount_limit',
                'restricted_usage',
            ])],
            'rules.*.parameters' => ['sometimes', 'array'],
            'rules.*.is_active' => ['sometimes', 'boolean'],

            'tiers' => ['required_if:discount_type,tiered', 'array'],
            'tiers.*.min_completed_orders' => ['required_with:tiers', 'integer', 'min:0'],
            'tiers.*.discount_percent' => ['required_with:tiers', 'numeric', 'min:0', 'max:100'],

            'restricted_user_ids' => ['sometimes', 'array'],
            'restricted_user_ids.*' => ['integer', 'exists:buyers,id'],
        ];
    }
}
