<?php

namespace App\Http\Controllers\Admin\InviteCodeHistory;

use App\InviteCodeHistory;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InviteCodeHistoryController extends Controller
{
    public function index(Request $request)
    {
        $inviteCodeHistories = InviteCodeHistory::with('inviteCode', 'user', 'order', 'points');

        $keyword = $request->search;
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

        if ($fromDate) {
            $inviteCodeHistories->where(function ($query) use ($fromDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($toDate) {
            $inviteCodeHistories->where(function ($query) use ($toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($keyword) {
            $inviteCodeHistories->where(function ($query) use ($keyword) {
                $query->where('id', "$keyword")
                    ->orWhereHas('user', function ($q) use ($keyword) {
                        $q->where('users.nickname', 'like', "%$keyword%");
                    })
                    ->orWhereHas('inviteCode', function ($q) use ($keyword) {
                        $q->whereHas('user', function ($sq) use ($keyword) {
                            $sq->where('users.nickname', 'like', "%$keyword%");
                        });
                    });
            });
        }

        $inviteCodeHistories = $inviteCodeHistories->paginate($request->limit ?: 10);

        return view('admin.invite_code_histories.index', compact('inviteCodeHistories'));
    }

    public function show(InviteCodeHistory $inviteCodeHistory)
    {
        return view('admin.invite_code_histories.show', compact('inviteCodeHistory'));
    }
}
