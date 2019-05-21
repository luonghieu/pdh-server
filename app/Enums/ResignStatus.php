<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ResignStatus extends Enum
{
    const NULL = null;
    const PENDING = 1;
    const APPROVED = 2;
}
