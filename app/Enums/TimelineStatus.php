<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TimelineStatus extends Enum
{
    const PUBLIC = 0;
    const PRIVATE = 1;

    /**
     * Get the description for an enum value
     *
     * @param $value
     * @return string
     */
    public static function getDescription($value): string
    {
        switch ($value) {
            case self::PRIVATE:
                return '非公開';
                break;
            case self::PUBLIC:
                return '公開';
                break;

            default:break;
        }

        return parent::getDescription($value);
    }
}
