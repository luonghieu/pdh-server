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
        $orderBy = $request->only('user_id', 'order_id', 'created_at', 'type');
        $keyword = $request->search;
        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

        $points = Point::where(
            [
               ['type', '=', PointType::INVITE_CODE],
            ],
            [
                ['type', '=', PointType::EVICT],
                ['invite_code_history_id', '<>', null],
            ]
        );

        if ($keyword) {
            $points->where(function ($q) use ($keyword) {
                $q->whereHas('user', function ($sq) use ($keyword) {
                    $sq->where('id', "$keyword")->orWhere('nickname', 'like', "%$keyword%");
                });
            });
        }

        $points = $points->get();

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

        $collection = $collection->reject(function ($item) use ($fromDate, $toDate) {
            $bool = false;
            $createdAt = Carbon::parse($item['created_at']);

            if ($fromDate && $toDate) {
                $bool = ($createdAt >= $fromDate && $createdAt <= $toDate) !== true;
            } elseif ($fromDate) {
                $bool = ($createdAt >= $fromDate) !== true;
            } elseif ($toDate) {
                $bool = ($createdAt <= $toDate) !== true;
            }

            return $bool;
        })->values();

        if (!empty($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $isDesc = ($value == 'asc') ? false : true;

                switch ($key) {
                    case 'user_id':
                        $collection = $collection->sortBy($key, SORT_REGULAR, $isDesc);
                        break;
                    case 'order_id':
                        $collection = $collection->sortBy($key, SORT_REGULAR, $isDesc);
                        break;
                    case 'created_at':
                        $collection = $collection->sortBy($key, SORT_REGULAR, $isDesc);
                        break;
                    case 'type':
                        $collection = $collection->sortBy($key, SORT_REGULAR, $isDesc);
                        break;
                    
                    default:break;
                }

            }
        }

        $total = $collection->count();
        $costEnterprises = $collection->forPage($request->page, $request->limit ?: 10);

        $costEnterprises = new LengthAwarePaginator($costEnterprises, $total, $request->limit ?: 10);
        $costEnterprises = $costEnterprises->withPath('');

        return view('admin.cost_enterprises.index', compact('costEnterprises'));
    }
}