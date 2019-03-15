<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InviteCodeHistory extends Model
{

    public function inviteCode()
    {
        return $this->belongsTo(InviteCode::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'receive_user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
