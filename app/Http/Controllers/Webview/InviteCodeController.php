<?php

namespace App\Http\Controllers\Webview;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Auth;

class InviteCodeController extends Controller
{
    public function inviteCode(Request $request)
    {
        try {
            if ($request->has('access_token')) {
                $user = JWTAuth::setToken($request->access_token)->toUser();
                if ($user) {
                    Auth::loginUsingId($user->id);

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
                    return view('webview.invite_codes.get_invite_code', compact('inviteCode'));
                }
            } else {
                return abort(403);
            }
        } catch (\Exception $e) {
            return abort(403);
        }
    }
}
