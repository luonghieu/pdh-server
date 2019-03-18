<?php

namespace App\Http\Controllers\Admin\CostEnterprise;

use App\Http\Controllers\Controller;
use App\Enums\PointType;
use App\Point;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CostEnterpriseController extends Controller
{
    public function index(Request $request)
    {
        $points = Point::where('type', PointType::INVITE_CODE)
            ->orWhere([
                ['type', '=', PointType::EVICT],
                ['invite_code_history_id', '<>', null],
            ])
            ->get();

        $arr = [];
        foreach ($points as $key => $point) {
            array_push($arr, $point);

            $histories = $point->histories;
            if ($histories) {
                foreach ($histories as $value) {
                    array_push($arr, $value);
                }
            }
        }

        $collection = collect($arr);

        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

        if ($fromDate) {
            $collection = $collection->where('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $collection = $collection->where('created_at', '<=', $toDate);
        }

        $total = $collection->count();
        $costEnterprises = $collection->forPage($request->page, $request->limit ?: 10);

        $costEnterprises = new LengthAwarePaginator($costEnterprises, $total, $request->limit ?: 10);
        $costEnterprises = $costEnterprises->withPath('');


        return view('admin.cost_enterprises.index', compact('costEnterprises'));
    }
}
