<?php

namespace App\Http\Controllers\Webview;

use App\Card;
use App\Http\Controllers\Controller;
use App\Services\Payment;
use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;

class CreditCardController extends Controller
{
    public function create(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->card) {
                $card = $user->card;
                return redirect(route('webview.show', ['card' => $card->id]));
            } else {
                return view('webview.create_card');
            }
        } else {
            try {
                if ($request->has('access_token')) {
                    $user = JWTAuth::setToken($request->access_token)->toUser();
                    if ($user) {
                        Auth::loginUsingId($user->id);
                    }

                    return redirect(route('webview.create'));
                } else {
                    return abort(403);
                }
            } catch (\Exception $e) {
                return abort(403);
            }
        }
    }

    public function addCard(Request $request)
    {
        $user = Auth::user();
        $accessToken = JWTAuth::fromUser($user);

        $rules = [
            'number_card' => 'required|regex:/[0-9]{0,16}/',
            'month' => 'required|numeric',
            'year' => 'required|numeric',
            'card_cvv' => 'required|regex:/[0-9]{3,4}/',
        ];

        $validator = validator($request->all(), $rules);

        $numberCardVisa = preg_match("/^4[0-9]{12}(?:[0-9]{3})?$/", $request->number_card);
        $numberMasterCard = preg_match("/^5[1-5][0-9]{14}$/", $request->number_card);

        $numberAmericanExpress = preg_match("/^3[47][0-9]{13,14}$/", $request->number_card);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => trans('messages.action_not_performed')]);
        }
        $currentMonth = Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        if ($currentMonth >= $request->month && $currentYear >= $request->year) {
            return response()->json(['success' => false, 'error' => trans('messages.action_not_performed')]);
        }

        if ($numberCardVisa || $numberMasterCard || $numberAmericanExpress) {
            $input = request()->only([
                'number_card',
                'month',
                'year',
                'card_cvv',
            ]);

            $response = $this->createToken($input, $accessToken);

            if ($response->getStatusCode() != 200) {
                return response()->json(['success' => false, 'error' => trans('messages.action_not_performed')]);
            }
            if ($user->card) {
                $card = $user->card;

                return response()->json(['success' => true, 'url' => 'cheers://adding_card?result=1']);
            }
        } else {
            return response()->json(['success' => false, 'error' => trans('messages.action_not_performed')]);
        }
    }

    public function show(Card $card)
    {
        return view('webview.show', compact('card'));
    }

    public function edit(Request $request, Card $card)
    {
        return view('webview.edit', compact('card'));
    }

    private function createToken($input, $accessToken)
    {
        $cardService = new Payment();

        $card = $cardService->createToken([
            "card" => [
                "number" => $input['number_card'],
                "exp_month" => $input['month'],
                "exp_year" => $input['year'],
                "cvc" => $input['card_cvv'],
            ],
        ]);

        $param = $card->id;

        $client = new Client();
        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => ['token' => $param],
            'allow_redirects' => false,
        ];

        $response = $client->post(route('cards.create'), $option);

        return $response;
    }
}
