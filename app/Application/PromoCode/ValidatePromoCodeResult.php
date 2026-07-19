<?php

declare(strict_types=1);

namespace App\Application\PromoCode;

final readonly class ValidatePromoCodeResult
{
    private function __construct(
        public bool $valid,
        public ?string $code,
        public float $discount,
        public float $subtotal,
        public float $total,
        public ?string $error,
    ) {}

    public static function success(string $code, float $discount, float $subtotal): self
    {
        return new self(
            valid: true,
            code: $code,
            discount: $discount,
            subtotal: $subtotal,
            total: $subtotal - $discount,
            error: null,
        );
    }

    public static function failure(string $errorCode, float $subtotal = 0.0): self
    {
        return new self(
            valid: false,
            code: null,
            discount: 0.0,
            subtotal: $subtotal,
            total: $subtotal,
            error: $errorCode,
        );
    }
}
