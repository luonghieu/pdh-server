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
        if (self::OPEN === $value) {
            return '提案中';
        } elseif (self::ACTIVE === $value) {
            return '予約確定';
        } elseif (self::PROCESSING === $value) {
            return '合流中';
        } elseif (self::DONE === $value) {
            return '解散中';
        } elseif (self::TIMEOUT === $value || self::CANCELED === $value || self::DENIED === $value) {
            return 'マッチング不成立';
        }

        return parent::getDescription($value);
    }
}
