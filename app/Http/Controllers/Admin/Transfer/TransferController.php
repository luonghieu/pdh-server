<?php

namespace App\Http\Controllers\Admin\Transfer;

use App\Enums\BankAccountType;
use App\Enums\PointType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Point;
use App\Services\CSVExport;
use App\Services\LogService;
use App\Transfer;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function getTransferedList(Request $request)
    {
        $adminType = UserType::ADMIN;
        $keyword = $request->search;
        $with['user'] = function ($query) {
            return $query->withTrashed();
        };
        $with[] = 'order';

        $transfers = Point::with($with)
            ->where('is_transfered', true)
            ->where(function ($q) use ($adminType) {
                $q->whereHas('user', function ($query) use ($adminType) {
                    $query->withTrashed()
                        ->where('type', '<>', $adminType)
                        ->where([
                            ['points.type', '=', PointType::ADJUSTED],
                            ['points.is_cast_adjusted', '=', true],
                        ]);
                })
                ->orWhereHas('order', function ($query) {
                    $query->whereNull('deleted_at')
                        ->where('points.type', PointType::RECEIVE);
                });
            })
            ->orderBy('updated_at', 'DESC');

        if ($request->from_date) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $transfers->where(function ($query) use ($fromDate) {
                $query->whereHas('order', function ($query) use ($fromDate) {
                    $query->where('created_at', '>=', $fromDate);
                })
                ->orWhere('created_at', '>=', $fromDate);
            });
        }

        if ($request->to_date) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $transfers->where(function ($query) use ($toDate) {
                $query->whereHas('order', function ($query) use ($toDate) {
                    $query->where('created_at', '<=', $toDate);
                })
                ->orWhere('created_at', '<=', $toDate);
            });
        }

        if ($keyword) {
            $transfers->whereHas('user', function ($query) use ($keyword) {
                $query->withTrashed()
                    ->where('id', "$keyword")
                    ->orWhere('nickname', 'like', "%$keyword%");
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
                    Carbon::parse($item->updated_at)->format('Y年m月d日'),
                    $item->user_id,
                    $item->user->nickname,
                    '¥ ' . $item->point,
                ];
            })->toArray();

            $sum = [
                '合計',
                '',
                '',
                '',
                '¥ ' . $transfers->sum('point'),
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
                $request->session()->flash('msg', trans('messages.server_error'));

                return redirect()->route('admin.points.transfered');
            }

            $file->output('transfered_list_' . Carbon::now()->format('Ymd_Hi') . '.csv');

            return;
        }
        $transfers = $transfers->paginate($request->limit ?: 10);

        return view('admin.transfers.transfered', compact('transfers'));
    }

    public function getNotTransferedList(Request $request)
    {
        $keyword = $request->search;
        $adminType = UserType::ADMIN;
        $with['user'] = function ($query) {
            return $query->withTrashed();
        };
        $with[] = 'order';

        $transfers = Point::with($with)
            ->where('is_transfered', false)
            ->where(function ($q) use ($adminType) {
                $q->whereHas('user', function ($query) use ($adminType) {
                    $query->withTrashed()
                        ->where('type', '<>', $adminType)
                        ->where([
                            ['points.type', '=', PointType::ADJUSTED],
                            ['points.is_cast_adjusted', '=', true],
                        ]);
                })
                ->orWhereHas('order', function ($query) {
                    $query->whereNull('deleted_at')
                        ->where('points.type', PointType::RECEIVE);
                });
            })
            ->orderBy('updated_at', 'DESC');

        if ($request->from_date) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $transfers->where(function ($query) use ($fromDate) {
                $query->whereHas('order', function ($query) use ($fromDate) {
                    $query->where('created_at', '>=', $fromDate);
                })
                ->orWhere('created_at', '>=', $fromDate);
            });
        }

        if ($request->to_date) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $transfers->where(function ($query) use ($toDate) {
                $query->whereHas('order', function ($query) use ($toDate) {
                    $query->where('created_at', '<=', $toDate);
                })
                ->orWhere('created_at', '<=', $toDate);
            });
        }

        if ($keyword) {
            $transfers->whereHas('user', function ($query) use ($keyword) {
                $query->withTrashed()
                    ->where('id', "$keyword")
                    ->orWhere('nickname', 'like', "%$keyword%");
            });
        }

        if ('transfers' == $request->submit) {
            $transfers = $transfers->select(DB::raw('sum(point) as sum_amount,  user_id'))->groupBy('user_id')->get();

            $header = [
                '1',
                '21',
                '0',
                '0000000000',
                str_pad('ｶ)ﾘｽﾃｨﾙ', 40, " ", STR_PAD_RIGHT),
                '都度連携',
                '1333',
                'ﾄｳｷﾖｳｻﾝｷﾖｳｼﾝｷﾝ',
                '012',
                str_pad('ｼﾝｼﾞﾕｸ', 15, " ", STR_PAD_RIGHT),
                '普通',
                str_pad('1023474', 7, "0", STR_PAD_LEFT),
                str_repeat(" ", 17),
            ];

            $data = collect($transfers)->map(function ($item) {
                return [
                    2,
                    $item->user->bankAccount ? str_pad($item->user->bankAccount->bank_code, 4, "0", STR_PAD_LEFT) : "",
                    str_repeat(" ", 15),
                    $item->user->bankAccount ? str_pad($item->user->bankAccount->branch_code, 3, "0", STR_PAD_LEFT) : "",
                    str_repeat(" ", 15),
                    str_repeat(" ", 4),
                    $item->user->bankAccount ? (BankAccountType::SAVING == $item->user->bankAccount->type ? '4' : $item->user->bankAccount->type) : " ",
                    $item->user->bankAccount ? str_pad($item->user->bankAccount->number, 7, "0", STR_PAD_LEFT) : "",
                    $item->user->bankAccount ? str_pad($item->user->bankAccount->holder, 30, " ", STR_PAD_RIGHT) : "",
                    str_pad($item->sum_amount, 10, "0", STR_PAD_LEFT),
                    0,
                    str_repeat(" ", 10),
                    str_repeat(" ", 10),
                    str_repeat(" ", 9),
                ];
            })->toArray();

            $trailer = [
                8,
                str_pad(count($data), 6, "0", STR_PAD_LEFT),
                str_pad($transfers->sum('point'), 12, "0", STR_PAD_LEFT),
                str_repeat(" ", 101),
            ];

            $end = [
                9,
                str_repeat(" ", 119),
            ];

            array_push($data, $trailer, $end);

            try {
                $file = CSVExport::toCSV($data, $header);
            } catch (\Exception $e) {
                LogService::writeErrorLog($e);
            }

            $file->output('non_transfered_list' . Carbon::now()->format('Ymd_Hi') . '.dat');

            return;
        }
        $transfers = $transfers->paginate($request->limit ?: 10);

        return view('admin.transfers.non_transfer', compact('transfers'));
    }

    public function changeTransfers(Request $request)
    {
        if ($request->has('transfer_ids')) {
            $transferIds = $request->transfer_ids;

            $checkTransferExist = Point::whereIn('id', $transferIds)
                ->where('is_transfered', false)
                ->where(function ($query) {
                    $query->orWhere('type', PointType::RECEIVE)
                        ->orWhere([
                            ['points.type', '=', PointType::ADJUSTED],
                            ['points.is_cast_adjusted', '=', true],
                        ]);
                })
                ->exists();

            try {
                if ($checkTransferExist) {
                    \DB::beginTransaction();
                    $transfers = Point::whereIn('id', $transferIds);
                    $transfers->update(['is_transfered' => true]);

                    $transfers = $transfers->groupBy('user_id')->selectRaw('sum(point) as sum, user_id');

                    foreach ($transfers->cursor() as $transfer) {
                        $user = $transfer->user;
                        $user->total_point += $transfer->sum;
                        $user->point -= $transfer->sum;
                        $user->save();

                        $data['point'] = -$transfer->sum;
                        $data['balance'] = $user->point;
                        $data['user_id'] = $transfer->user_id;
                        $data['type'] = PointType::TRANSFER;

                        $point = new Point;

                        $point->createPoint($data, true);
                    }

                    \DB::commit();

                    return redirect(route('admin.transfers.transfered'));
                } else {
                    \DB::beginTransaction();
                    $transfers = Point::whereIn('id', $transferIds);
                    $transfers->update(['is_transfered' => false]);
                    $transfers = $transfers->groupBy('user_id')->selectRaw('sum(point) as sum, user_id');

                    foreach ($transfers->cursor() as $transfer) {
                        $user = $transfer->user;
                        $user->total_point -= $transfer->sum;
                        $user->point += $transfer->sum;
                        $user->save();

                        $data['point'] = $transfer->sum;
                        $data['balance'] = $user->point;
                        $data['user_id'] = $transfer->user_id;
                        $data['type'] = PointType::ADJUSTED;

                        $point = new Point;
                        $point->createPoint($data, true);
                    }

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
