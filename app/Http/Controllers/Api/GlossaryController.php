<?php

namespace App\Http\Controllers\Api;

use App\BodyType;
use App\Enums\CohabitantType;
use App\Enums\DrinkVolumeType;
use App\Enums\OrderPaymentMethod;
use App\Enums\OrderPaymentStatus;
use App\Enums\PaymentRequestStatus;
use App\Enums\PointType;
use App\Enums\ProviderType;
use App\Enums\SiblingsType;
use App\Enums\SmokingType;
use App\Enums\UserGender;
use App\Enums\UserType;
use App\Job;
use App\Jobs\PointSettlement;
use App\Notifications\AutoChargeFailed;
use App\Notifications\AutoChargeFailedLineNotify;
use App\Notifications\AutoChargeFailedWorkchatNotify;
use App\Order;
use App\Point;
use App\Prefecture;
use App\Salary;
use App\Transfer;
use App\User;
use Carbon\Carbon;

class GlossaryController extends ApiController
{
    public function glossary()
    {

        $now = Carbon::now();
        $orders = Order::where(function ($query) {
            $query->where('payment_status', OrderPaymentStatus::REQUESTING)
                ->orWhere('payment_status', OrderPaymentStatus::PAYMENT_FAILED);
        })
            ->where('payment_requested_at', '<=', $now->copy()->subHours(3))
            ->where(function($query) {
                $query->whereHas('user', function ($q) {
                    $q->where(function ($sQ) {
                        $sQ->where('payment_suspended', false)
                            ->orWhere('payment_suspended', null);
                    });
                })->orWhere('payment_method', OrderPaymentMethod::DIRECT_PAYMENT);
            })->get();

        foreach ($orders as $order) {
            if (!$order->user->trashed()) {
                if ($order->payment_method == OrderPaymentMethod::DIRECT_PAYMENT) {
                    $user = $order->user;
                    if ($user->point > $order->total_point) {
                        $order->settle();

                        $order->paymentRequests()->update(['status' => PaymentRequestStatus::CLOSED]);

                        $order->payment_status = OrderPaymentStatus::PAYMENT_FINISHED;
                        $order->paid_at = $now;
                        $order->update();

                        $adminId = User::where('type', UserType::ADMIN)->first()->id;

                        $order = $order->load('paymentRequests');

                        $paymentRequests = $order->paymentRequests;

                        $receiveAdmin = 0;

                        foreach ($paymentRequests as $paymentRequest) {
                            $cast = $paymentRequest->cast;

                            $receiveCast = round($paymentRequest->total_point * $cast->cost_rate);
                            $receiveAdmin += round($paymentRequest->total_point * (1 - $cast->cost_rate));

                            $this->createTransfer($order, $paymentRequest, $receiveCast);

                            // receive cast
                            $this->createPoint($receiveCast, $paymentRequest->cast_id, $order);
                        }

                        // receive admin
                        $this->createPoint($receiveAdmin, $adminId, $order);
                    } else {
                        if (!$order->send_warning) {
                            $delay = Carbon::now()->addSeconds(3);
                            $user->notify(new AutoChargeFailedWorkchatNotify($order));
                            $user->notify((new AutoChargeFailedLineNotify($order))->delay($delay));

                            if (ProviderType::LINE == $user->provider) {
                                $user->notify(new AutoChargeFailed($order));
                            }

                            $order->send_warning = true;
                            $order->payment_status = OrderPaymentStatus::PAYMENT_FAILED;
                            $order->save();
                        }
                    }
                } else {
                    PointSettlement::dispatchNow($order->id);
                }
            }
        }
        dd(123);

        $drinkVolumes = [];
        $smokings = [];
        $siblings = [];
        $cohabitants = [];
        $genders = [];

        foreach (DrinkVolumeType::toArray() as $value) {
            $drinkVolumes[] = ['id' => $value, 'name' => DrinkVolumeType::getDescription($value)];
        }

        $data['drink_volumes'] = $drinkVolumes;

        foreach (SmokingType::toArray() as $value) {
            $smokings[] = ['id' => $value, 'name' => SmokingType::getDescription($value)];
        }

        $data['smokings'] = $smokings;

        foreach (SiblingsType::toArray() as $value) {
            $siblings[] = ['id' => $value, 'name' => SiblingsType::getDescription($value)];
        }

        $data['siblings'] = $siblings;

        foreach (CohabitantType::toArray() as $value) {
            $cohabitants[] = ['id' => $value, 'name' => CohabitantType::getDescription($value)];
        }

        $data['cohabitants'] = $cohabitants;

        foreach (UserGender::toArray() as $value) {
            $genders[] = ['id' => $value, 'name' => UserGender::getDescription($value)];
        }

        $data['genders'] = $genders;

        $data['prefectures'] = Prefecture::supported()->get(['id', 'name'])->toArray();

        $hometowns = Prefecture::all(['id', 'name']);
        $data['hometowns'] = $hometowns->prepend($hometowns->pull(48))->toArray();

        $data['body_types'] = BodyType::all(['id', 'name'])->toArray();

        $data['salaries'] = Salary::all(['id', 'name'])->toArray();

        $data['jobs'] = Job::all(['id', 'name'])->toArray();

        $data['order_options'] = config('common.order_options');

        $data['payment']['service'] = config('common.payment_service') == 'stripe' || config('common.payment_service') == 'square' ? 'internal' : 'external';

        $data['payment']['url'] = '';

        $data['enable_invite_code_banner'] = true;

        if ($token = request()->bearerToken()) {
            $user = auth('api')->setToken($token)->user();

            if ($user) {
                if ($data['payment']['service'] == 'internal') {
                    $data['payment']['url'] = route('webview.create');
                } else {
                    $paramsArray = [
                        'clientip' => env('TELECOM_CREDIT_CLIENT_IP'),
                        'usrtel' => $user->phone,
                        'usrmail' => 'question.cheers@gmail.com',
                        'user_id' => $user->id,
                        'redirect_url' => 'cheers://registerSuccess'
                    ];

                    $queryString = http_build_query($paramsArray);

                    $data['payment']['url'] = env('TELECOM_CREDIT_VERIFICATION_URL') . '?' . $queryString;
                }
            }
        }

        return $this->respondWithData($data);
    }

    private function createTransfer($order, $paymentRequest, $receiveCast)
    {
        $transfer = new Transfer;
        $transfer->order_id = $order->id;
        $transfer->user_id = $paymentRequest->cast_id;
        $transfer->amount = $receiveCast;
        $transfer->save();
    }

    private function createPoint($receive, $id, $order)
    {
        $user = User::find($id);

        $point = new Point;
        $point->point = $receive;
        $point->balance = $user->point + $receive;
        $point->user_id = $user->id;
        $point->order_id = $order->id;
        $point->type = PointType::RECEIVE;
        $point->status = true;
        $point->save();

        $user->point += $receive;
        $user->update();
    }
}
