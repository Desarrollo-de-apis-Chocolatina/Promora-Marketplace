<?php

namespace App\Domain\ValueObjects;

readonly class ValidationResult
{
    public function __construct(
        public bool $isValid,
        public ?string $errorCode = null
    ) {
    }

    public static function valid(): self
    {
        return new self(true);
    }

    public static function invalid(string $errorCode): self
    {
        return new self(false, $errorCode);
    }
}
