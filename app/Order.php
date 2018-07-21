<?php

namespace App;

use App\Enums\CastOrderStatus;
use App\Enums\CastOrderType;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'prefecture_id',
        'address',
        'date',
        'start_time',
        'end_time',
        'duration',
        'total_cast',
        'temp_point',
        'class_id',
        'type',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function casts()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.status', CastOrderStatus::ACCEPTED);
    }

    public function nominees()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.type', CastOrderType::NOMINEE)->withTimestamps();
    }

    public function candidates()
    {
        return $this->belongsToMany(Cast::class)
            ->where('cast_order.type', CastOrderType::CANDIDATE);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function deny($userId)
    {
        $user = User::find($userId);
        try {
            $this->nominees()->updateExistingPivot($user, ['status' => CastOrderStatus::DENIED], false);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
