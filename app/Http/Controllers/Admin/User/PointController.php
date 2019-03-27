<?php

namespace App\Http\Controllers\Admin\User;

use App\Enums\PointCorrectionType;
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
    public function sumAmount($points)
    {
        $pointIds = $points->where('type', '<>', PointType::ADJUSTED)->pluck('id');
        $sumAmount = Payment::whereIn('point_id', $pointIds)->sum('amount');

        return $sumAmount;
    }

    public function sumPointPay($points)
    {
        $sumPointPay = $points->sum(function ($product) {
            $sum = 0;
            if ($product->is_pay) {
                $sum += $product->point;
            }

            return $sum;
        });

        return $sumPointPay;
    }

    public function sumPointBuy($points)
    {
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

            if ($product->is_invite_code) {
                $sum += $product->point;
            }

            return $sum;
        });

        return $sumPointBuy;
    }

    public function getPointHistory(User $user, Request $request)
    {
        $keyword = $request->search_point_type;
        $pointTypes = [
            0 => '全て', // all
            PointType::BUY => 'ポイント購入',
            PointType::PAY => 'ポイント決済',
            PointType::AUTO_CHARGE => 'オートチャージ',
            PointType::ADJUSTED => '調整',
            PointType::EVICT => 'ポイント失効',
        ];

        $pointCorrectionTypes = [
            PointCorrectionType::ACQUISITION => '取得ポイント',
            PointCorrectionType::CONSUMPTION => '消費ポイント',
        ];

        $points = $user->points()->with('payment', 'order')->where('status', Status::ACTIVE);

        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;
        $limit = $request->limit;

        if ($fromDate) {
            $points->where(function ($query) use ($fromDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($toDate) {
            $points->where(function ($query) use ($toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($keyword) {
            if ('0' != $keyword) {
                $points->where(function ($query) use ($keyword) {
                    $query->where('type', $keyword);
                });
            }
        }

        $points = $points->orderBy('created_at', 'DESC');
        $pointsExport = $points->get();
        $points = $points->paginate($request->limit ?: 10);

        $sumAmount = $this->sumAmount($points);
        $sumPointBuy = $this->sumPointBuy($points);
        $sumPointPay = -$this->sumPointPay($points);
        $sumNonTransfer = $sumPointBuy - $sumPointPay;

        if ('export' == $request->submit) {
            $data = collect($pointsExport)->map(function ($item) {
                return [
                    Carbon::parse($item->created_at)->format('Y年m月d日'),
                    PointType::getDescription($item->type),
                    ($item->is_buy || $item->is_auto_charge) ? $item->id : '-',
                    ($item->is_pay) ? $item->order->id : '-',
                    ($item->is_adjusted || !$item->payment || $item->is_invite_code) ? '-' : '¥ ' . number_format($item->payment->amount),
                    ($item->is_buy || $item->is_auto_charge || $item->is_adjusted || $item->is_invite_code) ? $item->point : '',
                    ($item->is_pay) ? (-$item->point) : '-',
                    $item->balance,
                ];
            })->toArray();

            $sumPointBuyExport = $this->sumPointBuy($pointsExport);
            $sumPointPayExport = -$this->sumPointPay($pointsExport);
            $sumNonTransferExport = $sumPointBuyExport - $sumPointPayExport;

            $sum = [
                '合計',
                '-',
                '-',
                '-',
                '¥ ' . number_format($this->sumAmount($pointsExport)),
                $sumPointBuyExport,
                $sumPointPayExport,
                $sumNonTransferExport,
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
                $request->session()->flash('msg', trans('messages.server_error'));

                return redirect()->route('admin.users.points_history', compact('user'));
            }
            $file->output('history_point_of_user_' . $user->fullname . '_' . Carbon::now()->format('Ymd_Hi') . '.csv');

            return;
        }

        return view('admin.users.points_history', compact(
            'user', 'points', 'sumAmount',
            'sumPointPay', 'sumPointBuy', 'sumNonTransfer',
            'pointTypes', 'pointCorrectionTypes')
        );
    }

    public function changePoint(User $user, Request $request)
    {
        $rules = [
            'point' => 'regex:/^[0-9]+$/',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        switch ($request->correction_type) {
            case PointCorrectionType::ACQUISITION:
                $point = $request->point;
                break;
            case PointCorrectionType::CONSUMPTION:
                $point = -$request->point;
                break;

            default:break;
        }

        $newPoint = $user->point + $point;

        $input = [
            'point' => $point,
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
            DB::rollBack();
            LogService::writeErrorLog($e);

            $request->session()->flash('msg', trans('messages.server_error'));
        }

        return response()->json(['success' => true]);
    }
}
