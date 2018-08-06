<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PointType extends Enum
{
    const BUY = 1;
    const PAY = 2;
    const AUTO_CHARGE = 3;
    const ADJUSTED = 4;

    /**
     * Get the description for an enum value
     *
     * @param $value
     * @return string
     */
    public static function getDescription($value): string
    {
        if (self::BUY === $value) {
            return 'ポイント購入';
        } elseif (self::PAY === $value) {
            return 'ポイント決済';
        } elseif (self::AUTO_CHARGE === $value) {
            return 'オートチャージ';
        } elseif (self::ADJUSTED === $value) {
            return '調整';
        }

        return parent::getDescription($value);
    }
}
