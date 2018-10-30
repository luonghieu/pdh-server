<?php

namespace App\Http\Controllers\Api\Cast;

use App\Enums\CastTransferStatus;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class CastController extends ApiController
{
    public function confirmTransfer(Request $request)
    {
        $cast = $this->guard()->user();
        if ($cast->cast_transfer_status != CastTransferStatus::APPROVED) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        $cast->cast_transfer_status = null;
        $cast->save();

        return $this->respondWithNoData(trans('messages.transfer_to_cast_succeed'));
    }
}