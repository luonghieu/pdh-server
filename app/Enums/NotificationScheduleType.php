<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationScheduleType extends Enum
{
    const ALL = 1;
    const GUEST = 2;
    const CAST = 3;
}
