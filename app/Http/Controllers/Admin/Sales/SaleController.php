<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Enums\PointType;
use App\Http\Controllers\Controller;
use App\Point;
use App\Services\CSVExport;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $pointType = $request->search_point_type;

        $pointTypes = [
            0 => 'All',
            PointType::PAY => 'ポイント決済',
            PointType::ADJUSTED => '調整',
            PointType::EVICT => 'ポイント失効',
        ];

        $sales = Point::whereIn('type', [PointType::PAY, PointType::ADJUSTED, PointType::EVICT]);

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $sales->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $sales->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($request->has('search_point_type') && (0 != $request->search_point_type)) {
            $sales->where(function ($query) use ($pointType) {
                $query->where('type', "$pointType");
            });
        }

        $sales = $sales->orderBy('created_at', 'DESC');
        $salesExport = $sales;
        $sales = $sales->paginate($request->limit ?: 10);

        $totalPoint = $sales->sum('point');

        if ('export' == $request->submit) {
            $salesExport = $salesExport->get();

            $data = collect($salesExport)->map(function ($item) {
                return [
                    $item->order_id,
                    Carbon::parse($item->created_at)->format('Y年m月d日'),
                    $item->user_id,
                    ($item->user) ? $item->user->fullname : "",
                    PointType::getDescription($item->type),
                    number_format($item->point),
                ];
            })->toArray();

            $sum = [
                '合計',
                '-',
                '-',
                '-',
                '-',
                number_format($totalPoint),
            ];

            array_push($data, $sum);

            $header = [
                '予約ID',
                '日付',
                'ユーザーID',
                'ユーザー名',
                '取引種別',
                '消費ポイント',
            ];

            try {
                $file = CSVExport::toCSV($data, $header);
            } catch (\Exception $e) {
                LogService::writeErrorLog($e);
                $request->session()->flash('msg', trans('messages.server_error'));

                return redirect()->route('admin.sales.index');
            }
            $file->output('Revenue_list_' . Carbon::now()->format('Ymd_Hi') . '.csv');

            return;
        }

        return view('admin.sales.index', compact('sales', 'totalPoint', 'pointTypes'));
    }
}
