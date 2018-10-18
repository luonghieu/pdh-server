<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationSchedule extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'send_date',
        'status',
    ];
}
