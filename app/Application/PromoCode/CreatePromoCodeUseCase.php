<?php

declare(strict_types=1);

namespace App\Application\PromoCode;

use App\Domain\PromoCode\Contracts\PromoCodeRepositoryInterface;
use App\Domain\PromoCode\PromoCode;

final class CreatePromoCodeUseCase
{
    public function __construct(
        private readonly PromoCodeRepositoryInterface $repository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): PromoCode
    {
        return $this->repository->create($data);
    }
}
