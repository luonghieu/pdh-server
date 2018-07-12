<?php

namespace App;

use App\Enums\UserType;
use App\Traits\HasParentModel;

class Cast extends User
{
    use HasParentModel;

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($query) {
            $query->where('type', UserType::CAST);
        });
    }
}
