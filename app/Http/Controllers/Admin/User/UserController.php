<?php

namespace App\Http\Controllers\Admin\User;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $orderBy = $request->only('id', 'status');
        $keyword = $request->search;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $users = User::where('type', '<>', UserType::ADMIN);

        if (isset($fromDate) && isset($toDate)) {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();

            $users->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            });
        }

        if (isset($keyword)) {
            $users->where(function ($query) use ($keyword) {
                $query->where('id', "$keyword")
                    ->orWhere('fullname', 'like', "%$keyword%");
            });
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $users->orderBy($key, $value);
            }
        } else {
            $users->orderBy('created_at', 'DESC');
        }

        $users = $users->paginate($request->limit ?: 10);

        return view('admin.users.index', compact('users'));
    }

    public function show()
    {
        return view('admin.users.show');
    }
}
