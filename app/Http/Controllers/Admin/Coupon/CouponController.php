<?php

namespace App\Http\Controllers\Admin\Coupon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Coupon;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function index(Request $request) {
        $keyword = $request->search;

        $coupons = Coupon::with('users');

        if ($request->has('search')) {
            $coupons->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            });
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $coupons->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $coupons->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        $coupons = $coupons->paginate();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create() {
        return view('admin.coupons.create');
    }

    public function store(Request $request) {
        $rules = [
            'name' => 'required|string',
            'type' => 'required|numeric|in:1,2',
            'point' => 'numeric|required_if:type,1|nullable',
            'time' => 'numeric|required_if:type,2|nullable',
            'note' => 'string',
            'is_filter_after_created_date' => 'numeric|nullable',
            'filter_after_created_date' => 'numeric|nullable',
            'is_filter_order_duration' => 'numeric|nullable',
            'filter_order_duration' => 'numeric|nullable',
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        $input = request()->only([
            'name',
            'type',
            'point',
            'time',
            'note',
            'is_filter_after_created_date',
            'filter_after_created_date',
            'is_filter_order_duration',
            'filter_order_duration',
        ]);

        $coupon = new Coupon;
        $coupon = $coupon->create($input);

        if ($coupon) {
            return redirect(route('admin.coupons.index'));
        } else {
            $request->session()->flash('err', trans('messages.server_error'));

            return redirect(route('admin.coupons.create'));
        }
    }

    public function delete(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index');
    }

    public function history(Coupon $coupon) {
        $historyCoupons = $coupon->orders()->paginate();

        return view('admin.coupons.history', compact('coupon', 'historyCoupons'));
    }

    public function show(Coupon $coupon) {
        return view('admin.coupons.show', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $rules = [
            'name' => 'string',
            'type' => 'numeric|in:1,2',
            'point' => 'numeric|required_if:type,1|nullable',
            'time' => 'numeric|required_if:type,2|nullable',
            'note' => 'string',
            'is_filter_after_created_date' => 'numeric|nullable',
            'filter_after_created_date' => 'numeric|nullable',
            'is_filter_order_duration' => 'numeric|nullable',
            'filter_order_duration' => 'numeric|nullable',
        ];

        $validator = validator(request()->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput();
        }

        $input = request()->only([
            'name',
            'type',
            'point',
            'time',
            'note',
            'is_filter_after_created_date',
            'filter_after_created_date',
            'is_filter_order_duration',
            'filter_order_duration',
        ]);

        if (!isset($input['is_filter_after_created_date'])) {
            $input['is_filter_after_created_date'] = 0;
        }

        if (!isset($input['is_filter_order_duration'])) {
            $input['is_filter_order_duration'] = 0;
        }

        try {
            $coupon->update($input);

            return redirect()->route('admin.coupons.index');

        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            $request->session()->flash('err', trans('messages.server_error'));

            return redirect()->route('admin.coupons.show', compact('coupon'));
        }
    }
}
