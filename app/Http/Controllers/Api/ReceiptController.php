<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ReceiptResource;
use App\Point;
use App\Receipt;
use App\Services\LogService;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;

class ReceiptController extends ApiController
{
    public function create(Request $request)
    {
        $rules = [
            'point_id' => 'required',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $point = Point::with('payment')->find($request->point_id);

        if (!$point) {
            return $this->respondErrorMessage(trans('messages.point_not_found'), 404);
        }

        $receipt = Receipt::where('point_id', $request->point_id)->first();

        if ($receipt) {
            return $this->respondErrorMessage(trans('messages.receipt_exists'), 409);
        }

        try {
            $name = null;
            $content = null;
            if ($request->name) {
                $name = $request->name;
            }

            if ($request->get('content')) {
                $content = $request->get('content');
            }

            DB::beginTransaction();
            $receipt = Receipt::create([
                'point_id' => $request->point_id,
                'date' => Carbon::today(),
                'name' => $name,
                'content' => $content,
            ]);

            $data = [
                'no' => $receipt->id,
                'name' => $name,
                'content' => $content,
                'amount' => $point->payment->amount,
                'created_at' => $receipt->created_at,
            ];

            $pdf = PDF::loadView('pdf.invoice', $data)->setPaper('a4', 'portrait');
            $fileName = 'receipts/' . Uuid::generate()->string . '.pdf';
            \Storage::put($fileName, $pdf->output(), 'public');

            $receipt->file = $fileName;
            $receipt->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return $this->respondWithData(ReceiptResource::make($receipt));
    }
}
