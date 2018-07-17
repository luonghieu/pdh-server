<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class MessageType extends Enum
{
    const SYSTEM = 1;
    const MESSAGE = 2;
    const IMAGE = 3;

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
