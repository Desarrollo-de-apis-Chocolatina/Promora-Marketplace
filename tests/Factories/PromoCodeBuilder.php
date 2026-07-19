<?php

namespace Tests\Factories;

use App\Domain\PromoCode\PromoCode;
use App\Domain\PromoCode\PromoCodeStatus;

class PromoCodeBuilder
{
    private string $code = 'PROMO10';
    private string $type = 'percent';
    private float $value = 10.0;
    private PromoCodeStatus $status = PromoCodeStatus::ACTIVE;
    private ?string $validFrom = null;
    private ?string $validUntil = null;

    public function withCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function withType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function withValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function withStatus(PromoCodeStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function withValidFrom(?string $validFrom): self
    {
        $this->validFrom = $validFrom;
        return $this;
    }

    public function withValidUntil(?string $validUntil): self
    {
        $this->validUntil = $validUntil;
        return $this;
    }

    public function build(): PromoCode
    {
        return new PromoCode(
            $this->code,
            $this->type,
            $this->value,
            $this->status,
            $this->validFrom,
            $this->validUntil
        );
    }
}
