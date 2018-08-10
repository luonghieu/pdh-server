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

        if ($request->has('search')) {
            $casts->where(function ($query) use ($keyword) {
                $query->where('id', "$keyword")
                    ->orWhere('nickname', 'like', "%$keyword%");
            });
        }

        $casts = $casts
            ->with('castRanking')
            ->whereHas('castRanking')
            ->orderBy('point', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate();

        return view('admin.cast_ranking.index', compact('casts'));
    }
}
