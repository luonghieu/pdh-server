<?php

namespace App\Http\Controllers\Webview;

use App\Services\LogService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Auth;
use App\InviteCode;
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
                        do {
                            $code = generateInviteCode();
                            $checkCodeExist = InviteCode::where('code', $code)->first();
                        } while($checkCodeExist);

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
            LogService::writeErrorLog($e);
            return abort(403);
        }
    }
}
