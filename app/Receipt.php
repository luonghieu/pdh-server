<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Receipt extends Model
{
    protected $fillable = [
        'point_id',
        'date',
        'address',
        'content'
    ];

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function getFileAttribute($value)
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
