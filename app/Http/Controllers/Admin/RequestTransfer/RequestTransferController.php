<?php

namespace App\Http\Controllers\Admin\RequestTransfer;

use App\Enums\CastTransferStatus;
use App\Http\Controllers\Controller;
use App\Notifications\RequestTransferNotify;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RequestTransferController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->search;
        $orderBy = $request->only('nickname', 'request_transfer_date');

        if ($request->has('transfer_type') && (CastTransferStatus::DENIED == $request->transfer_type)) {
            $casts = User::where([
                'cast_transfer_status' => CastTransferStatus::DENIED,
            ]);
        } else {
            $casts = User::where([
                'cast_transfer_status' => CastTransferStatus::PENDING,
            ]);
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $casts->where(function ($query) use ($fromDate) {
                $query->whereDate('request_transfer_date', '>=', $fromDate);
            });
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $casts->where(function ($query) use ($toDate) {
                $query->whereDate('request_transfer_date', '<=', $toDate);
            });
        }

        if ($request->has('search') && $request->search) {
            $casts->where(function ($query) use ($keyword) {
                $query->where('nickname', 'like', "%$keyword%")
                    ->orWhere('id', $keyword);
            });
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $casts->orderBy($key, $value);
            }
        } else {
            $casts->orderBy('created_at', 'DESC');
        }

        $casts = $casts->paginate($request->limit ?: 10);

        return view('admin.request_transfer.index', compact('casts'));
    }

    public function show(User $cast)
    {
        return view('admin.request_transfer.show', compact('cast'));
    }

    public function update(User $cast, Request $request)
    {
        try {
            if ($request->has('transfer_request_status')) {
                $cast->cast_transfer_status = $request->transfer_request_status;
                $cast->class_id = 1;
                $cast->save();

                $cast->notify(new RequestTransferNotify());

                if (CastTransferStatus::APPROVED == $request->transfer_request_status) {
                    return redirect(route('admin.casts.index'));
                } else {
                    return redirect(route('admin.request_transfer.index', ['transfer_type' => CastTransferStatus::DENIED]));
                }
            }
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            $request->session()->flash('err', trans('messages.server_error'));

            return redirect(route('admin.request_transfer.show', ['cast' => $cast->id]));
        }
    }
}
