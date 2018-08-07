<?php

namespace App;

use App\Enums\PaymentStatus;
use App\Services\LogService;
use App\Services\Payment as StripePayment;
use Illuminate\Database\Eloquent\Model;
use App\Traits\FailedPaymentHandle;

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

            $request = [
                'amount' => $this->amount,
                'customer' => $this->user->stripe_id,
                'source' => $this->card->card_id,
                'description' => "Charge user for buying point order id of {$this->point_id}",
            ];

            try {
                $stripe = new StripePayment;
                $charge = $stripe->charge($request);

                // update order payment status
                $this->charge_id = $charge->id;
                $this->charge_at = now();
                $this->status = PaymentStatus::DONE;
                $this->save();

                return $charge;
            } catch (\Stripe\Error\Card $e) {
                // Since it's a decline, \Stripe\Error\Card will be caught
                $body = $e->getJsonBody();
                $error = $body['error'];

                $this->createFailedPaymentRecord($this->id, 1, $error);

                LogService::writeErrorLog($e);
            } catch (\Stripe\Error\RateLimit $e) {
                // Too many requests made to the API too quickly
            } catch (\Stripe\Error\InvalidRequest $e) {
                // Invalid parameters were supplied to Stripe's API
            } catch (\Stripe\Error\Authentication $e) {
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
            } catch (\Stripe\Error\ApiConnection $e) {
                // Network communication with Stripe failed
            } catch (\Stripe\Error\Base $e) {
                // Display a very generic error to the user, and maybe send
                // yourself an email
            } catch (\Exception $e) {
                // Something else happened, completely unrelated to Stripe
                LogService::writeErrorLog($e);
            }
        }
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
