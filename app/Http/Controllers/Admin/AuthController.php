<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function login(LoginRequests $request)
    {
        $credentials = request()->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->is_admin) {
                return redirect()->route('admin.accounts.index');
            } else {
                $request->session()->flash('msg', trans('auth.noaccess'));

                return redirect()->route('admin.login');
            }
        } else {
            $request->session()->flash('msg', trans('messages.login_error'));

            return redirect()->route('admin.login');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flash('msg', trans('messages.logout_success'));

        return redirect()->route('admin.login');
    }
}
