<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ProviderType extends Enum
{
    const FACEBOOK = 'facebook';
    const LINE = 'line';

    /**
     * Get the description for an enum value
     *
     * @param $value
     * @return string
     */
    public static function getDescription($value): string
    {
        if ($value === self::FACEBOOK) {
            return 'Facebook';
        }

        if ($value === self::LINE) {
            return 'Line';
        }


        return parent::getDescription($value);
    }
}
