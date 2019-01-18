<?php

namespace App;

use App\Enums\PaymentStatus;
use App\Services\LogService;
use App\Traits\FailedPaymentHandle;
use Illuminate\Database\Eloquent\Model;
use App\Services\TelecomCredit;

class Payment extends Model
{
    use FailedPaymentHandle;

    public function scopeOpen($query)
    {
        return $query->where('status', PaymentStatus::OPEN);
    }

    public function charge()
    {
        if (PaymentStatus::OPEN == $this->status) {
            $this->load(['user']);
            $user = $this->user;

            // do not call Stripe if payment is suspended
            if ($user->payment_suspended || !$user->tc_send_id) {
                return false;
            }

            $request = [
                'amount' => $this->amount,
                'customer' => $user->tc_send_id,
                'user_id' => $user->id,
                'payment_id' => $this->id,
            ];

            try {
                $paymentService = new TelecomCredit;
                $charge = $paymentService->charge($request);

                if (!$charge) {
                    return false;
                }

                // update order payment status
                $this->charge_at = now();
                $this->status = PaymentStatus::DONE;
                $this->save();

                return true;
            } catch (\Exception $e) {
                // Something else happened, completely unrelated to Stripe
                LogService::writeErrorLog($e);

                return false;
            }
        }

        return false;
    }

    protected function handleStripeException($e)
    {
        $body = $e->getJsonBody();
        $error = $body['error'];

        $this->createFailedPaymentRecord($this->id, 1, $error);
    }

    public function point()
    {
        return $this->belongsTo(Point::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
