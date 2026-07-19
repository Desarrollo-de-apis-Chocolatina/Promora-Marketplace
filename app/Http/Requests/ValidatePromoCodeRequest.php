<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePromoCodeRequest extends FormRequest
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
            'code' => ['required', 'string'],
            'order' => ['required', 'array'],
            'order.id' => ['nullable'],
            'order.subtotal' => ['required', 'numeric', 'min:0'],
            'order.category_id' => ['required', 'integer', 'exists:service_categories,id'],
            'buyer' => ['required', 'array'],
            'buyer.id' => ['required', 'integer', 'exists:buyers,id'],
        ];
    }
}
