<?php

namespace App\Http\Controllers;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentRequestStatus;
use App\Enums\PointType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Jobs\PointSettlement;
use App\Order;
use App\Point;
use App\Services\LogService;
use App\Transfer;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use JWTAuth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $client = new Client();
        $user = Auth::user();

        $accessToken = JWTAuth::fromUser($user);

        $option = [
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
            'form_params' => [],
            'allow_redirects' => false,
        ];

        $response = $client->get(route('guest.index'), $option);

        $result = $response->getBody();
        $contents = $result->getContents();
        $contents = json_decode($contents, JSON_NUMERIC_CHECK);
        $orders = $contents['data'];

        return view('web.orders.list', compact('orders'));
    }

    public function history(Request $request, $orderId)
    {
        $user = Auth::user();
        $order = Order::whereIn('status', [OrderStatus::OPEN, OrderStatus::ACTIVE, OrderStatus::PROCESSING, OrderStatus::DONE])
            ->where('user_id', $user->id)
            ->with(['user', 'casts', 'nominees', 'tags'])
            ->find($orderId);

        return view('web.orders.history', compact('order','user'));
    }

    public function pointSettlement(Request $request, $id)
    {
        $now = Carbon::now();
        $order = Order::where('payment_status', OrderPaymentStatus::REQUESTING)->find($id);
        if (!$order) {
            return redirect()->back();
        }
        try {
            DB::beginTransaction();
            $order->settle();
            $order->paymentRequests()->update(['status' => PaymentRequestStatus::CLOSED]);

            $order->payment_status = OrderPaymentStatus::PAYMENT_FINISHED;
            $order->paid_at = $now;
            $order->update();

            $adminId = User::where('type', UserType::ADMIN)->first()->id;

            $order = $order->load('paymentRequests');

            $paymentRequests = $order->paymentRequests;

            $receiveAdmin = 0;
            $castPercent = config('common.cast_percent');

            foreach ($paymentRequests as $paymentRequest) {
                $receiveCast = $paymentRequest->total_point * $castPercent;
                $receiveAdmin += $paymentRequest->total_point * (1 - $castPercent);

                $this->createTransfer($order, $paymentRequest, $receiveCast);

                // receive cast
                $this->createPoint($receiveCast, $paymentRequest->cast_id, $order);
            }

            // receive admin
            $this->createPoint($receiveAdmin, $adminId, $order);

            DB::commit();

            return response()->json(['success' => true, 'message' => trans('messages.payment_completed')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);
            return response()->json(['success' => false], 500);
        }
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
