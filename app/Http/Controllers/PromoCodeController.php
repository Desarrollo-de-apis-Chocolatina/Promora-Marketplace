<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\PromoCode\CreatePromoCodeUseCase;
use App\Application\PromoCode\ValidatePromoCodeUseCase;
use App\Http\Requests\StorePromoCodeRequest;
use App\Http\Requests\ValidatePromoCodeRequest;
use Illuminate\Http\JsonResponse;

class PromoCodeController extends Controller
{
    public function __construct(
        private readonly ValidatePromoCodeUseCase $validateUseCase,
        private readonly CreatePromoCodeUseCase $createUseCase,
    ) {}

    public function validate(ValidatePromoCodeRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->validateUseCase->execute(
            $data['code'],
            $data['order']['id'] ?? uniqid('order_', more_entropy: true),
            (float) $data['order']['subtotal'],
            $data['order']['category_id'],
            $data['buyer']['id'],
            $data['current_orders'] ?? [],
        );

        return response()->json([
            'valid' => $result->valid,
            'code' => $result->code,
            'discount' => round($result->discount, 2),
            'subtotal' => round($result->subtotal, 2),
            'total' => round($result->total, 2),
            'error' => $result->error,
        ], $result->valid ? 200 : 422);
    }

    public function store(StorePromoCodeRequest $request): JsonResponse
    {
        $promoCode = $this->createUseCase->execute($request->validated());

        return response()->json([
            'code' => $promoCode->code,
            'discount_type' => $promoCode->type,
            'discount_value' => $promoCode->value,
            'status' => $promoCode->status->value,
            'max_discount_amount' => $promoCode->maxDiscountAmount,
        ], 201);
    }
}
