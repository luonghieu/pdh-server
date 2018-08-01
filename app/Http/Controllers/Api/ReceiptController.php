<?php

namespace App\Http\Controllers\Api;

use App\Enums\PointType;
use App\Http\Resources\ReceiptResource;
use App\Point;
use App\Receipt;
use App\Services\LogService;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Webpatser\Uuid\Uuid;

class ReceiptController extends ApiController
{
    public function create(Request $request)
    {
        $rules = [
            'point_id' => 'required',
            'address' => 'required',
            'content' => 'required',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();
        $receipt = Receipt::where('point_id', $request->point_id)->first();

        if ($receipt) {
            return $this->respondErrorMessage(trans('messages.receipt_exists'), 409);
        }

        $point = Point::with('payment')->find($request->point_id);

        if (!$point) {
            return $this->respondErrorMessage(trans('messages.point_not_found'), 404);
        }

        try {
            DB::beginTransaction();
            $receipt = Receipt::create([
                'point_id' => $request->point_id,
                'date' => Carbon::today(),
                'address' => $request->address,
                'content' => $request->get('content')
            ]);

            $data = [
                'no' => $receipt->id,
                'name' => $user->nickname,
                'amount' => $point->payment->amount,
                'created_at' => $receipt->created_at,
                'type' => PointType::getDescription($receipt->point->type)
            ];

            $pdf = PDF::loadView('pdf.invoice', $data)->setPaper('a4', 'portrait');
            $fileName= 'receipts/' . Uuid::generate()->string . '.pdf';
            \Storage::put($fileName, $pdf->output(), 'public');

            $receipt->file = $fileName;
            $receipt->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);
            return $this->respondServerError();
        }

        return ReceiptResource::make($receipt);
    }
}
