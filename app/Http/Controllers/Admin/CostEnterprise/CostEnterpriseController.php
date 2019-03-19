<?php

namespace App\Http\Controllers\Admin\CostEnterprise;

use App\Http\Controllers\Controller;
use App\Enums\PointType;
use App\Point;
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

        $costEnterprises = collect($arr);
        $total = $costEnterprises->count();
        $costEnterprises = $costEnterprises->forPage($request->page, $request->limit ?: 10);

        $costEnterprises = new LengthAwarePaginator($costEnterprises, $total, $request->limit ?: 10);
        $costEnterprises = $costEnterprises->withPath('');


        return view('admin.cost_enterprises.index', compact('costEnterprises'));
    }
}
