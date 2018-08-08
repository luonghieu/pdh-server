<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\TransferStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\TransferResource;
use App\Transfer;
use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    public function payments(Request $request)
    {
        $user = $this->guard()->user();

        $payments = Transfer::where('user_id', $user->id)->where('status', TransferStatus::CLOSED);

        $payments = $payments->latest()->paginate($request->per_page)->appends($request->query());

        return $this->respondWithData(TransferResource::collection($payments));
    }
}
