<?php

namespace App\Http\Controllers\Admin\Transfer;

use App\Http\Controllers\Controller;
use App\Services\CSVExport;
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

            if ($checkTransferExist) {
                Transfer::whereIn('id', $transferIds)->update(['transfered_at' => now()]);

                return redirect(route('admin.transfers.non_transfers'));
            } else {
                Transfer::whereIn('id', $transferIds)->update(['transfered_at' => null]);

                return redirect(route('admin.transfers.transfered'));
            }
        }
    }
}
