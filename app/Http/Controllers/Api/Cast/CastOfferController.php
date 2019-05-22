<?php

namespace App\Http\Controllers\Api\Cast;

use App\Cast;
use App\Enums\CastOrderStatus;
use App\Enums\CastOrderType;
use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\UserType;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\OrderResource;
use App\Notifications\CastCreateOffer;
use App\Order;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CastOfferController extends ApiController
{
    public function create( Request $request )
    {
        $rules = [
            'date'       => 'required|date|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration'   => 'required|numeric|min:1|max:10',
            'address'    => 'required',
            'user_id'    => 'required',
        ];

        $validator = validator( $request->all(), $rules );

        if ($validator->fails()) {
            return $this->respondWithValidationError( $validator->errors()->messages() );
        }

        $orderStartTime = Carbon::parse( $request->date . ' ' . $request->start_time );
        $orderEndTime   = $orderStartTime->copy()->addMinutes( $request->duration * 60 );

        $user = $this->guard()->user();
        $prevOrders = Order::whereIn('status', [
            OrderStatus::OPEN,
            OrderStatus::ACTIVE,
            OrderStatus::PROCESSING,
            OrderStatus::OPEN_FOR_GUEST
        ])->whereHas('nominees', function ( $query ) use ( $user ) {
            $query->where( 'user_id', $user->id );
        } )->get();

        foreach ($prevOrders as  $order) {
            $startTime = Carbon::parse( $order->date . ' ' . $order->start_time );
            $endTime   = $startTime->copy()->addMinutes( $order->duration * 60 );

            if ($endTime->gte($orderStartTime)) {
                return $this->respondErrorMessage( trans( 'messages.time_invalid' ), 400 );
            }
        }

        $guest = User::where( 'id', $request->user_id )->where( 'type', UserType::GUEST )->first();
        if ( ! $guest) {
            return $this->respondErrorMessage( trans( 'messages.user_not_found' ), 404 );
        }


        $cast = Cast::find( $user->id );

        try {
            $nightTime  = $this->nightTime( $orderStartTime, $orderEndTime );
            $allowance  = $this->allowance( $nightTime );
            $orderPoint = $this->orderPoint( $cast, $request->duration );

            $order = $guest->orders()->create( [
                'date'          => $request->date,
                'start_time'    => $request->start_time,
                'end_time'      => $orderEndTime,
                'duration'      => $request->duration,
                'class_id'      => $cast->class_id,
                'address'       => $request->address,
                'temp_point'    => $orderPoint + $allowance,
                'prefecture_id' => $cast->prefecture_id,
                'total_cast'    => 1,
                'status'        => OrderStatus::OPEN_FOR_GUEST,
                'type'          => OrderType::NOMINATION,
            ] );

            $order->nominees()->attach( $cast->id, [
                'type'        => CastOrderType::NOMINEE,
                'status'      => CastOrderStatus::ACCEPTED,
                'cost'        => $cast->cost,
                'temp_point'  => $orderPoint + $allowance,
                'accepted_at' => now()
            ] );

            $guest->notify( new CastCreateOffer( $order->id ) );

            return $this->respondWithData( OrderResource::make( $order ) );
        } catch ( \Exception $e ) {
            LogService::writeErrorLog( $e );

            return $this->respondServerError();
        }
    }

    private function nightTime( $startedAt, $stoppedAt )
    {
        $nightTime = 0;
        $startDate = Carbon::parse( $startedAt );
        $endDate   = Carbon::parse( $stoppedAt );

        $allowanceStartTime = Carbon::parse( '00:01:00' );
        $allowanceEndTime   = Carbon::parse( '04:00:00' );

        $startDay = Carbon::parse( $startDate )->startOfDay();
        $endDay   = Carbon::parse( $endDate )->startOfDay();

        $timeStart = Carbon::parse( Carbon::parse( $startDate->format( 'H:i:s' ) ) );
        $timeEnd   = Carbon::parse( Carbon::parse( $endDate->format( 'H:i:s' ) ) );

        $allowance = false;

        if ($startDay->diffInDays( $endDay ) != 0 && $endDate->diffInMinutes( $endDay ) != 0) {
            $allowance = true;
        }

        if ($timeStart->between( $allowanceStartTime, $allowanceEndTime ) || $timeEnd->between( $allowanceStartTime,
                $allowanceEndTime )) {
            $allowance = true;
        }

        if ($timeStart < $allowanceStartTime && $timeEnd > $allowanceEndTime) {
            $allowance = true;
        }

        if ($allowance) {
            $nightTime = $endDate->diffInMinutes( $endDay );
        }

        return $nightTime;
    }

    private function allowance( $nightTime )
    {
        if ($nightTime) {
            return 4000;
        }

        return 0;
    }

    private function orderPoint( $cast, $orderDuration )
    {
        $cost          = $cast->cost;
        $orderDuration = $orderDuration * 60;

        return ( $cost / 2 ) * floor( $orderDuration / 15 );
    }
}
