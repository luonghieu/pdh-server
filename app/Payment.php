<?php

namespace App;

use App\Enums\PaymentStatus;
use App\Services\LogService;
use App\Services\Payment as StripePayment;
use App\Traits\FailedPaymentHandle;
use Illuminate\Database\Eloquent\Model;

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
            if ($user->payment_suspended) {
                return false;
            }

            $request = [
                'amount' => $this->amount,
                'customer' => $user->stripe_id,
                'source' => $this->card->card_id,
                'description' => "Charge user for buying point order id of {$this->point_id}",
            ];

            try {
                $stripe = new StripePayment;
                $charge = $stripe->charge($request);

                if ($charge->error) {
                    $error = (array) $charge->error;

                    $this->createFailedPaymentRecord($this->id, 1, $error);

                    $user->suspendPayment();

                    LogService::writeErrorLog($error['message']);

                    return false;
                }

                // update order payment status
                $this->charge_id = $charge->id;
                $this->charge_at = now();
                $this->status = PaymentStatus::DONE;
                $this->save();

                return $charge;
            } catch (\Stripe\Error\Base $e) {
                $body = $e->getJsonBody();
                $error = $body['error'];

                $this->createFailedPaymentRecord($this->id, 1, $error);

                $user->suspendPayment();

                LogService::writeErrorLog($e);

                return false;
            } catch (\Exception $e) {
                // Something else happened, completely unrelated to Stripe
                LogService::writeErrorLog($e);

                $user->suspendPayment();

                return false;
            }
        }

        return false;
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
