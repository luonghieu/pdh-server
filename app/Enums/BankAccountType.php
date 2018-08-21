<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class BankAccountType extends Enum
{
    const NORMAL = 1;
    const CHECKING = 2;
    const SAVING = 3;

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
