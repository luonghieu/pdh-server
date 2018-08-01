<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    public function cast()
    {
        return $this->belongsTo(Cast::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
