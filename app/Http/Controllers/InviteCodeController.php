<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Services\LogService;

class InviteCodeController extends Controller
{
    public function inviteCode()
    {
        try {
            $user = Auth::user();
            $inviteCode = $user->inviteCode;
            if (!$inviteCode) {
                $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

                $code = str_shuffle($permitted_chars);
                $code = substr($code, 0, 6);
                $data = [
                    'code' => $code,
                ];

                $user->inviteCode()->create($data);
                $inviteCode = $user->inviteCode()->first();
            }

            return view('web.invite_codes.get_invite_code', compact('inviteCode'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }
    }
}
