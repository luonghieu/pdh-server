<?php

namespace App\Http\Controllers\Admin;

use App\Cast;
use App\CastRanking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CastRankingController extends Controller
{
    public function index(Request $request)
    {

        $keyword = $request->search;
        $casts = Cast::query();
        $castRankings = CastRanking::get()->pluck('user_id');

        if ($request->has('search')) {
            $casts->where(function ($query) use ($keyword) {
                $query->where('id', "$keyword")
                    ->orWhere('nickname', 'like', "%$keyword%");
            });
        }

        $casts = $casts->whereIn('id', $castRankings)
            ->select('id', 'nickname', 'point')
            ->orderBy('point', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate();

        return view('admin.cast_ranking.index', compact('casts'));
    }
}
