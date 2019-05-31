<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Enums\ResignStatus;
use App\Enums\Status;
use App\Enums\UserType;

trait DeleteUser
{
    public function deleteUser($user)
    {
        $cards = $user->cards;
        $avatars = $user->avatars;

        if ($user->type == UserType::CAST) {
            $shifts = $user->shifts;

            if($shifts->first()) {
                $user->shifts()->detach();
            }
        }

        if($avatars->first()) {
            foreach ($avatars as $avatar) {
                $avatar->delete();
            }
        }

        if($cards->first()) {
            foreach ($cards as $card) {
                $card->delete();
            }
        }

        // Delete room 1-1
        // $rooms = DB::table('rooms')
        //     ->join('room_user', function ($join) use ($user) {
        //         $join->on('rooms.id', '=', 'room_user.room_id')
        //             ->where('room_user.user_id', '=', $user->id);
        //     })
        //     ->where('rooms.type', '=', RoomType::DIRECT);

        // $roomIds = $rooms->pluck('room_id')->toArray();

        // if ($rooms->exists()) {
        //     DB::table('room_user')->whereIn('room_id', $roomIds)->delete();
        //     DB::table('rooms')->whereIn('id', $roomIds)->delete();
        // }

        $user->facebook_id = null;
        $user->line_id = null;
        $user->line_user_id = null;
        $user->line_qr = null;
        $user->provider = null;
        $user->email = null;
        $user->password = null;
        $user->front_id_image = null;
        $user->back_id_image = null;
        $user->phone = null;
        $user->gender = null;
        $user->date_of_birth = null;
        $user->height = null;
        $user->salary_id = null;
        $user->body_type_id = null;
        $user->prefecture_id = null;
        $user->living_id = null;
        $user->post_code = null;
        $user->address = null;
        $user->hometown_id = null;
        $user->job_id = null;
        $user->drink_volume_type = null;
        $user->smoking_type = null;
        $user->siblings_type = null;
        $user->cohabitant_type = null;
        $user->intro = null;
        $user->intro_updated_at = null;
        $user->description = null;
        $user->note = null;
        $user->device_type = null;
        $user->request_transfer_date = null;
        $user->accept_request_transfer_date = null;
        $user->accept_verified_step_one_date = null;
        $user->status = Status::INACTIVE;
        $user->is_verified = 0;
        $user->is_guest_active = null;
        $user->cost = null;
        $user->total_point = null;
        $user->point = null;
        $user->working_today = null;
        $user->class_id = null;
        $user->stripe_id = null;
        $user->square_id = null;
        $user->tc_send_id = null;
        $user->is_online = null;
        $user->last_active_at = null;
        $user->payment_suspended = null;
        $user->campaign_participated = null;
        $user->is_multi_payment_method = null;
        $user->resign_status = ResignStatus::APPROVED;
        $user->resign_date = Carbon::now();

        $user->save();
        $user->delete();
    }
}
