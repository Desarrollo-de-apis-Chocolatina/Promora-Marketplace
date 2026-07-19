<?php

declare(strict_types=1);

namespace App\Domain\PromoCode\ValueObjects;

final readonly class ValidationResult
{
    private function __construct(
        public bool $isValid,
        public ?string $errorCode = null,
    ) {}

    public static function valid(): self
    {
        return new self(true);
    }

    public static function invalid(string $errorCode): self
    {
        return new self(false, $errorCode);
    }
}
