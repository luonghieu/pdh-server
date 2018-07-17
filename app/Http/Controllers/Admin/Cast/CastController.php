<?php

namespace App\Http\Controllers\Admin\Cast;

use App\Http\Controllers\Controller;
use App\Cast;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CastController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->search;
        $fromDate = Carbon::parse($request->from_date)->startOfDay();
        $toDate = Carbon::parse($request->to_date)->endOfDay();

        $casts = Cast::query();

        if ($request->has('from_date')) {
            $casts->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($request->has('to_date')) {
            $casts->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($request->has('search')) {
            $casts->where(function ($query) use ($keyword) {
                $query->where('id', "$keyword")
                    ->orWhere('nickname', 'like', "%$keyword%");
            });
        }

        $casts = $casts->orderBy('created_at', 'DESC')->paginate($request->limit ?: 10);

        return view('admin.casts.index', compact('casts'));
    }
}
