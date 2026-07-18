<?php

namespace Tests\Factories;

use App\Domain\ValueObjects\BuyerProfile;

class BuyerProfileBuilder
{
    private int $id = 1;
    private bool $isFirstOrder = false;

    public function withId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function firstOrder(bool $isFirstOrder = true): self
    {
        $this->isFirstOrder = $isFirstOrder;
        return $this;
    }

    public function build(): BuyerProfile
    {
        return new BuyerProfile(
            $this->id,
            $this->isFirstOrder
        );
    }
}
