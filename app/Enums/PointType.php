<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PointType extends Enum
{
    const BUY = 1;
    const PAY = 2;

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
