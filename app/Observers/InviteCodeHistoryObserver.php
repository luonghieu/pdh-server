<?php

namespace App\Observers;

use App\Enums\InviteCodeHistoryStatus;
use App\InviteCodeHistory;
use App\Notifications\AddedInvitePoint;

class InviteCodeHistoryObserver
{
    public function updated(InviteCodeHistory $inviteCodeHistory)
    {
        if ($inviteCodeHistory->getOriginal('status') != $inviteCodeHistory->status && $inviteCodeHistory->status == InviteCodeHistoryStatus::RECEIVED) {
            $userInvite = $inviteCodeHistory->inviteCode->user;
            $userInvite->notify(new AddedInvitePoint());

            $order = $inviteCodeHistory->order;
            $user = $order->user;
            $user->notify(new AddedInvitePoint(true));
        }
    }
}
