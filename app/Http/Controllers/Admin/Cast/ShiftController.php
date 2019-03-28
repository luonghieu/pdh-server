<?php

namespace App\Http\Controllers\Admin\Cast;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
class ShiftController extends Controller
{
    public function index(User $user)
    {
        $from = now()->copy()->startOfDay();
        $to = now()->copy()->addDays(6)->startOfDay();
        $updateShiftLatest = $user->shifts()->orderBy('shift_user.updated_at', 'DESC')->first();
        $shifts = $user->shifts()->whereBetween('date', [$from, $to])->get();

        return view('admin.casts.schedule', compact('user', 'shifts', 'updateShiftLatest'));
    }
}
