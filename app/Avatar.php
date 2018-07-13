<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Avatar extends Model
{
    protected $fillable = [
        'path',
        'thumbnail',
        'is_default'
    ];

    public function getPathAttribute($value)
    {
        if (empty($value)) {
            return '';
        }

        if (strpos($value, 'https') !== false) {
            return $value;
        }

        return Storage::url($value);
    }

    public function getThumbnailAttribute($value)
    {
        if (empty($value)) {
            return $this->avatar;
        }

        if (strpos($value, 'https') !== false) {
            return $value;
        }

        return Storage::url($value);
    }
}
