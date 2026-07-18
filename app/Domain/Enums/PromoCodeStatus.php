<?php

namespace App\Domain\Enums;

enum PromoCodeStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case PAUSED = 'paused';
    case EXPIRED = 'expired';
}
