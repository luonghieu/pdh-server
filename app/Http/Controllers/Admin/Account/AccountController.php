<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
        return view('admin.accounts.index');
    }

    public function show()
    {
        return view('admin.accounts.show');
    }
}
