<?php

namespace App\Http\Controllers\Admin\Transfer;

use App\Enums\PointType;
use App\Enums\TransferStatus;
use App\Http\Controllers\Controller;
use App\Point;
use App\Services\CSVExport;
use App\Services\LogService;
use App\Transfer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function getTransferedList(Request $request)
    {
        $transfers = Transfer::with('user', 'order')->whereNotNull('transfered_at');

        if ($request->from_date) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $transfers->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($request->to_date) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $transfers->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ('export' == $request->submit) {
            $transfers = $transfers->get();

            if (!$transfers->count()) {
                return redirect(route('admin.transfers.non_transfers'));
            }

            $data = collect($transfers)->map(function ($item) {
                return [
                    $item->order_id,
                    Carbon::parse($item->created_at)->format('Y年m月d日'),
                    $item->user_id,
                    $item->user->fullname,
                    '¥ ' . $item->amount,
                ];
            })->toArray();

            $sum = [
                '合計',
                '',
                '',
                '',
                '¥ ' . $transfers->sum('amount'),
            ];

            array_push($data, $sum);

            $header = [
                '予約ID',
                '日付',
                'ユーザーID',
                'ユーザー名',
                '振込金額',
            ];

            try {
                $data = encoderShiftJIS($data);
                $header = !($header = encoderShiftJIS([$header])) ? false : collect($header)->first();

                $file = CSVExport::toCSV($data, $header);
            } catch (\Exception $e) {
                LogService::writeErrorLog($e);
            }

            $file->output('transfered_list_' . Carbon::now()->format('Ymd_Hi') . '.csv');

            return;
        }
        $transfers = $transfers->orderBy('created_at', 'DESC')->paginate($request->limit ?: 10);

        return view('admin.transfers.transfered', compact('transfers'));
    }

    public function getNotTransferedList(Request $request)
    {
        $keyword = $request->search;

        $transfers = Transfer::with('user', 'order')->whereNull('transfered_at');

        if ($request->from_date) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $transfers->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($request->to_date) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $transfers->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($keyword) {
            $transfers->whereHas('user', function ($query) use ($keyword) {
                $query->where('id', "$keyword")
                    ->orWhere('fullname', 'like', "%$keyword%");
            });
        }

        if ('export' == $request->submit) {
            $transfers = $transfers->get();

            if (!$transfers->count()) {
                return redirect(route('admin.transfers.non_transfers'));
            }

            $data = collect($transfers)->map(function ($item) {
                return [
                    $item->order_id,
                    Carbon::parse($item->created_at)->format('Y年m月d日'),
                    $item->user_id,
                    $item->user->fullname,
                    '¥ ' . $item->amount,
                ];
            })->toArray();

            $sum = [
                '合計',
                '',
                '',
                '',
                '¥ ' . $transfers->sum('amount'),
            ];

            array_push($data, $sum);

            $header = [
                '予約ID',
                '日付',
                'ユーザーID',
                'ユーザー名',
                '振込金額',
            ];

            try {
                $file = CSVExport::toCSV($data, $header);
            } catch (\Exception $e) {
                LogService::writeErrorLog($e);
            }

            $file->output('non_transfered_list' . Carbon::now()->format('Ymd_Hi') . '.csv');

            return;
        }
        $transfers = $transfers->orderBy('created_at', 'DESC')->paginate($request->limit ?: 10);

        return view('admin.transfers.non_transfer', compact('transfers'));
    }

    public function changeTransfers(Request $request)
    {
        if ($request->has('transfer_ids')) {
            $transferIds = $request->transfer_ids;

            $checkTransferExist = Transfer::whereIn('id', $transferIds)->whereNull('transfered_at')->exists();

            try {
                if ($checkTransferExist) {
                    $transfers = Transfer::whereIn('id', $transferIds);
                    $pointIds = [];

                    \DB::beginTransaction();
                    foreach ($transfers->cursor() as $transfer) {
                        $user = $transfer->user;
                        $user->total_point += $transfer->amount;
                        $user->point -= $transfer->amount;
                        $user->save();

                        $pointId = Point::where('user_id', $transfer->user_id)
                            ->where('order_id', $transfer->order_id)
                            ->where('type', PointType::RECEIVE)
                            ->pluck('id')
                            ->first();

                        $pointIds = array_push($pointIds, $pointId);
                    }

                    Point::whereIn('id', $pointIds)->update(['type' => PointType::TRANSFER]);

                    $transfers->update(['transfered_at' => now(), 'status' => TransferStatus::CLOSED]);
                    \DB::commit();

                    return redirect(route('admin.transfers.transfered'));
                } else {
                    $transfers = Transfer::whereIn('id', $transferIds);
                    $pointIds = [];

                    \DB::beginTransaction();
                    foreach ($transfers->cursor() as $transfer) {
                        $user = $transfer->user;
                        $user->total_point -= $transfer->amount;
                        $user->point += $transfer->amount;
                        $user->save();

                        $pointId = Point::where('user_id', $transfer->user_id)
                            ->where('order_id', $transfer->order_id)
                            ->where('type', PointType::TRANSFER)
                            ->pluck('id')
                            ->first();

                        $pointIds = array_push($pointIds, $pointId);
                    }

                    Point::whereIn('id', $pointIds)->update(['type' => PointType::RECEIVE]);

                    $transfers->update(['transfered_at' => null, 'status' => TransferStatus::OPEN]);
                    \DB::commit();

                    return redirect(route('admin.transfers.non_transfers'));
                }
            } catch (\Exception $e) {
                \DB::rollBack();
                LogService::writeErrorLog($e);
            }
        }
    }
}
