<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CastOfferStatus extends Enum
{
    const PENDING = 1;
    const DENIED = 2;
    const APPROVED = 3;
}
