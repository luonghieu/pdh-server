<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CastTransferStatus extends Enum
{
    const PENDING = 1;
    const DENIED = 2;
    const APPROVED = 3;
    const OFFICIAL = 4;
    const VERIFIED_STEP_ONE = 5;
}
