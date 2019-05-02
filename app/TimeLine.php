<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TimeLine extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'image',
        'location',
        'hidden',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorites()
    {
        return $this->hasMany(TimeLineFavorite::class);
    }

    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return '';
        }

        if (strpos($value, 'https') !== false) {
            return $value;
        }

        return Storage::url($value);
    }
}
