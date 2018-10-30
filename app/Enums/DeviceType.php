<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class DeviceType extends Enum
{
    const ALL = 0;
    const IOS = 1;
    const ANDROID = 2;
    const WEB = 3;

    public static function getDescription($value): string
    {
        if (self::WEB == $value) {
            return 'Web';
        } elseif (self::ANDROID == $value) {
            return 'Android';
        } elseif (self::IOS == $value) {
            return 'IOS';
        } elseif (self::ALL == $value) {
            return '全て';
        }

        return parent::getDescription($value);
    }
}
