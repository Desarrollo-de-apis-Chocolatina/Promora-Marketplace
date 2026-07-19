<?php

declare(strict_types=1);

namespace App\Domain\PromoCode;

enum PromoCodeStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case EXPIRED = 'expired';
}
