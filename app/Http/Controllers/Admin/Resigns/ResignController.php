<?php

namespace App\Http\Controllers\Admin\Resigns;

use App\Enums\ResignStatus;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ResignController extends Controller
{
    public function index(Request $request)
    {
        if ($request->resign_status == ResignStatus::PENDING) {
            $users = User::where('resign_status', ResignStatus::PENDING);
        } else {
            $users = User::onlyTrashed()->where('resign_status', ResignStatus::PENDING);
        }

        $users = $users->paginate();
        return view('admin.resigns.index', compact('users'));
    }

    public function show(Request $request, $id)
    {
        $user = User::withTrashed()->where('resign_status', '<>', ResignStatus::NOT_RESIGN)->find($id);

        return view('admin.resigns.show', compact('user'));
    }
}
