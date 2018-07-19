<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderType extends Enum
{
    const NOMINATED_CALL = 1;
    const CALL = 2;
    const NOMINATION = 3;

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
