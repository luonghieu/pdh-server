<?php

namespace App\Http\Controllers\Api;

use App\Card;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\CardResource;
use App\Services\LogService;
use App\Services\Payment;
use Illuminate\Http\Request;

class CardController extends ApiController
{
    protected $payment;

    public function __construct()
    {
        $this->payment = new Payment;
    }

    public function create(Request $request)
    {
        $rules = [
            'token' => 'required',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithValidationError($validator->errors()->messages());
        }

        $user = $this->guard()->user();

        try {
            if (!$user->stripe_id) {
                $attributes = [
                    'description' => $user->fullname,
                ];

                $customer = $this->payment->createCustomer($attributes);
                $customerId = $customer->id;

                $user->stripe_id = $customerId;
                $user->save();
            } else {
                $customerId = $user->stripe_id;
            }

            $customer = $this->payment->getCustomer($customerId);
            $isDefault = $customer->default_source ? false : true;

            $card = $customer->sources->create(['source' => $request->token]);

            if (!in_array($card->brand, Card::BRANDS)) {
                $customer->sources->retrieve($card->id)->delete();

                return $this->respondErrorMessage(trans('messages.payment_method_not_supported'));
            }

            $cardAttributes = [
                'card_id' => $card->id,
                'address_city' => $card->address_city,
                'address_country' => $card->address_country,
                'address_line1' => $card->address_line1,
                'address_line1_check' => $card->address_line1_check,
                'address_line2' => $card->address_line2,
                'address_state' => $card->address_state,
                'address_zip' => $card->address_zip,
                'address_zip_check' => $card->address_zip_check,
                'brand' => $card->brand,
                'country' => $card->country,
                'customer' => $card->customer,
                'cvc_check' => $card->cvc_check,
                'dynamic_last4' => $card->dynamic_last4,
                'exp_month' => $card->exp_month,
                'exp_year' => $card->exp_year,
                'fingerprint' => $card->fingerprint,
                'funding' => $card->funding,
                'last4' => $card->last4,
                'name' => $card->name,
                'tokenization_method' => $card->tokenization_method,
                'is_default' => $isDefault,
            ];

            $card = $user->cards()->create($cardAttributes);

            return $this->respondWithData(CardResource::make($card));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }

    public function destroy($id)
    {
        $user = $this->guard()->user();
        $card = $user->cards()->find($id);

        if (!$card) {
            return $this->respondErrorMessage(trans('messages.action_not_performed'), 422);
        }

        try {
            $customerId = $user->stripe_id;
            $customer = $this->payment->getCustomer($customerId);

            $customer->sources->retrieve($card->card_id)->delete();

            $card->delete();

            return $this->respondWithNoData(trans('messages.card_delete_success'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);

            return $this->respondServerError();
        }
    }

    public function index()
    {
        $user = $this->guard()->user();

        $cards = $user->cards()
            ->orderBy('is_default', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->respondWithData(CardResource::collection($cards));
    }
}
