<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationScheduleStatus extends Enum
{
    const SAVE = 1;
    const PUBLISH = 2;
    const UNPUBLISH = 3;
}
