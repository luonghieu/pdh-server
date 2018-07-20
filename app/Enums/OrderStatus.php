<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderStatus extends Enum
{
    const OPEN = 1;
    const ACTIVE = 2;
    const PROCESSING = 3;
    const DONE = 4;
    const CANCELED = 5;
    const DENIED = 6;
    const TIMEOUT = 7;

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
