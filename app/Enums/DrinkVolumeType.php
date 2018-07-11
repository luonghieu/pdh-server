<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class DrinkVolumeType extends Enum
{
    const YES = 1;
    const OCCASIONALLY = 2;
    const NO = 3;

    /**
     * Get the description for an enum value
     *
     * @param $value
     * @return string
     */
    public static function getDescription($value): string
    {
        if ($value === self::YES) {
            return '飲む';
        } elseif ($value === self::NO) {
            return '飲まない';
        } elseif ($value == self::OCCASIONALLY) {
            return 'たまに飲む';
        }

        return parent::getDescription($value);
    }
}
