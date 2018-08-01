<?php

namespace App\Http\Controllers\Api;

use App\Enums\PointType;
use App\Receipt;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReceiptController extends ApiController
{
    public function download($id)
    {

        $user = $this->guard()->user();
        $receipt = Receipt::with(['point' => function($q) {
            $q->with(['user', 'payment']);
        }])->whereHas('point', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->find($id);

        if (!$receipt) {
            return $this->respondErrorMessage(trans('messages.receipt_not_found'), 404);
        }

        $data = [
            'no' => $receipt->id,
            'name' => $receipt->point->user->nickname,
            'amount' => $receipt->point->payment->amount,
            'created_at' => $receipt->created_at,
            'type' => PointType::getDescription($receipt->point->type)
        ];

        $pdf = PDF::loadView('pdf.invoice', $data)->setPaper('a4', 'portrait');
        return $pdf->download("invoice.pdf");
    }
}
