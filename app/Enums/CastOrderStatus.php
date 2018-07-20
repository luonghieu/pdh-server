<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CastOrderStatus extends Enum
{
    const ACCEPTED = 1;
    const DENIED = 2;
    const CANCELED = 3;
    const TIMEOUT = 4;

    /**
     * Get the description for an enum value
     *
     * @param $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return parent::getDescription($value);
    }
}
