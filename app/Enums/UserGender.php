<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserGender extends Enum
{
    const MALE = 1;
    const FEMALE = 2;

    /**
     * Get the description for an enum value
     *
     * @param $value
     * @return string
     */
    public static function getDescription($value): string
    {
        if ($value === self::MALE) {
            return '男性';
        } elseif ($value === self::FEMALE) {
            return '女性';
        }

        return parent::getDescription($value);
    }
}
