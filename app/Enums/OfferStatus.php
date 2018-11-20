<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OfferStatus extends Enum
{
    const INACTIVE = 1;
    const ACTIVE = 2;
    const DONE = 3;

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
