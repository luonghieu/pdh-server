<?php

namespace App\Http\Controllers\Admin\User;

use App\Enums\PointType;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Payment;
use App\Point;
use App\Services\CSVExport;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function getPointHistory(User $user, Request $request)
    {
        $keyword = $request->search_point_type;
        $pointTypes = [
            0 => 'All',
            PointType::BUY => 'ポイント購入',
            PointType::PAY => 'ポイント決済',
            PointType::AUTO_CHARGE => 'オートチャージ',
            PointType::ADJUSTED => '調整',
        ];
        $points = $user->points()->with('payment', 'order')->where('status', Status::ACTIVE);

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

        if ($request->has('search_point_type') && (0 != $request->search_point_type)) {
            $points->where(function ($query) use ($keyword) {
                $query->where('type', "$keyword");
            });
        }

        $points = $points->orderBy('created_at', 'DESC');
        $pointsExport = $points->get();
        $points = $points->paginate();

        $pointIds = $points->where('type', '<>', PointType::ADJUSTED)->pluck('id');
        $sumAmount = Payment::whereIn('id', $pointIds)->sum('amount');

        $sumPointPay = $points->sum(function ($product) {
            $sum = 0;
            if ($product->is_pay) {
                $sum += $product->point;
            }
            return $sum;
        });

        $sumPointBuy = $points->sum(function ($product) {
            $sum = 0;
            if ($product->is_buy) {
                $sum += $product->point;
            }
            return $sum;
        });

        $sumBalance = $points->sum('balance');

        if ('export' == $request->submit) {
            $data = collect($pointsExport)->map(function ($item) {
                return [
                    Carbon::parse($item->created_at)->format('Y年m月d日'),
                    PointType::getDescription($item->type),
                    $item->id,
                    (PointType::ADJUSTED == $item->type) ? '-' : $item->order->id,
                    (PointType::ADJUSTED == $item->type) ? '-' : $item->payment->amount,
                    (PointType::BUY == $item->type) ? $item->point : '',
                    (PointType::PAY == $item->type) ? $item->point : '',
                    $item->balance,
                ];
            })->toArray();

            $sum = [
                '合計',
                '-',
                '-',
                '-',
                $sumAmount,
                $sumPointBuy,
                $sumPointPay,
                $sumBalance,
            ];

            array_push($data, $sum);

            $header = [
                '日付',
                '取引タイプ',
                '購入ID',
                '予約ID',
                '請求金額',
                '購入ポイント',
                '決済ポイント',
                '残高',
            ];
            try {
                $file = CSVExport::toCSV($data, $header);
            } catch (\Exception $e) {
                LogService::writeErrorLog($e);
                return $this->respondServerError();
            }
            $file->output('history_point_of_user_' . $user->fullname . '_' . Carbon::now()->format('Ymd_Hi') . '.csv');

            return;
        }

        return view('admin.users.points_history', compact('user', 'points', 'sumAmount', 'sumPointPay', 'sumPointBuy', 'sumBalance', 'pointTypes'));
    }

    public function changePoint(User $user, Request $request)
    {
        $newPoint = $request->point;
        $oldPoint = $user->point;
        $differencePoint = $newPoint - $oldPoint;

        $input = [
            'point' => $differencePoint,
            'balance' => $newPoint,
            'type' => PointType::ADJUSTED,
            'status' => Status::ACTIVE,
        ];

        try {
            DB::beginTransaction();

            $user->points()->create($input);

            $user->point = $newPoint;
            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return redirect(route('admin.users.points_history', ['user' => $user->id]));
    }
}
