<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\OrderPaymentStatus;
use App\Enums\ResignStatus;
use App\Http\Resources\UserResource;
use App\Services\LogService;
use Auth;
use Illuminate\Http\Request;

class ResignController extends ApiController
{
    public function create(Request $request)
    {
        $user = Auth::user();

        if ($user->resign_status != null) {
           return $this->respondErrorMessage(trans('messages.created_request_resign'), 409);
        }

        $isUnpaidOrder = $user->orders()->whereIn('status', [
            OrderStatus::OPEN,
            OrderStatus::ACTIVE,
            OrderStatus::PROCESSING,
        ])
        ->orWhere(function ($query) {
            $query->where('status', OrderStatus::DONE)
                ->where(function ($subQuery) {
                    $subQuery->where('payment_status', '!=', OrderPaymentStatus::PAYMENT_FINISHED)
                        ->orWhere('payment_status', OrderPaymentStatus::PAYMENT_FAILED);
                });
        })
        ->orWhere(function ($query) {
            $query->where('status', OrderStatus::CANCELED)
                ->where(function ($subQuery) {
                    $subQuery->where('payment_status', '!=', OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED)
                        ->whereNotNull('cancel_fee_percent');
                });
        })
        ->exists();

        if ($isUnpaidOrder) {
            return $this->respondErrorMessage(trans('messages.can_not_be_resign'), 403);
        }

        $input = $request->only('reason1', 'reason2', 'reason3');

        try {
            $firstResignDescription = null;
            foreach ($input as $key => $value) {
                if ($value != null) {
                    if ($key == 'reason3') {
                        $firstResignDescription = $firstResignDescription . $value;
                    } else {
                        $firstResignDescription = $firstResignDescription . $value . '|';
                    }
                }
            }

            $user->resign_status = ResignStatus::PENDING;
            $user->first_resign_description = $firstResignDescription;
            $user->second_resign_description = $request->other_reason;

            $user->save();
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }

        return $this->respondWithNoData(trans('message.resign_success'));
    }
}
