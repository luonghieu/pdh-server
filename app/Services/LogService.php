<?php

namespace App\Services;

class LogService
{
    public static function writeErrorLog($error)
    {
        \Log::error($error);
    }
}
