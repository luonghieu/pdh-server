<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationStyle extends Enum
{
    const DEFAULT = 0;
    const BALL = 1;
    const BAND = 2;

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
