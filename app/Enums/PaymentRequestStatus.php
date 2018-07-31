<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentRequestStatus extends Enum
{
    const OPEN = 1;
    const CLOSED = 2;

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
