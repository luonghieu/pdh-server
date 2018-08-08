<?php

namespace App\Http\Controllers\Admin\Point;

use App\Enums\PointType;
use App\Http\Controllers\Controller;
use App\Point;
use App\Payment;
use App\Services\CSVExport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->search_point_type;
        $pointTypes = [
            PointType::BUY => 'ポイント購入',
            PointType::AUTO_CHARGE => 'オートチャージ',
            PointType::ADJUSTED => '調整',
        ];

        $points = Point::whereIn('type', [PointType::BUY, PointType::AUTO_CHARGE, PointType::ADJUSTED]);

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $points->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $points->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($request->has('search_point_type')) {
            $points->where(function ($query) use ($keyword) {
                $query->where('type', "$keyword");
            });
        }

        $points = $points->orderBy('created_at', 'DESC');
        $pointsExport = $points->get();
        $points = $points->paginate();

        $pointIds = $points->where('type', '<>', PointType::ADJUSTED)->pluck('id');
        $sumAmount = Payment::whereIn('point_id', $pointIds)->sum('amount');

        $sumPointBuy = $points->sum(function ($product) {
            $sum = 0;
            if ($product->is_buy) {
                $sum += $product->point;
            }

            if ($product->is_auto_charge) {
                $sum += $product->point;
            }

            if ($product->is_adjusted) {
                $sum += $product->point;
            }

            return $sum;
        });

        if ('export' == $request->submit) {
            $data = collect($pointsExport)->map(function ($item) {
                return [
                    $item->id,
                    Carbon::parse($item->created_at)->format('Y年m月d日'),
                    $item->user_id,
                    $item->user->fullname,
                    PointType::getDescription($item->type),
                    (PointType::ADJUSTED == $item->type) ? '¥ ' . number_format($item->payment->amount) : '-',
                    $item->point,
                ];
            })->toArray();

            $sum = [
                '合計',
                '-',
                '-',
                '-',
                '-',
                '¥ ' . number_format($sumAmount),
                $sumPointBuy,
            ];

            array_push($data, $sum);

            $header = [
                '購入ID',
                '日付',
                'ユーサーID',
                'ユーサー名',
                '取引種別',
                '購入金額',
                '購入ポイント',
            ];
            try {
                $file = CSVExport::toCSV($data, $header);
            } catch (\Exception $e) {
                LogService::writeErrorLog($e);
                return $this->respondServerError();
            }
            $file->output('history_point_' . Carbon::now()->format('Ymd_Hi') . '.csv');

            return;
        }

        return view('admin.points.index', compact('points', 'pointTypes', 'sumAmount', 'sumPointBuy'));
    }
}
