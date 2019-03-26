@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <div class="info-table col-lg-8">
            <table class="table table-bordered">
              <!--  table-striped -->
              @php
                $pointInviteId = '';
                $pointReceiveId = '';
                foreach($inviteCodeHistory->points as $point)
                  if ($point->user_id == $inviteCodeHistory->inviteCode->user_id) {
                    $pointInviteId = $point->id;
                  } else {
                    $pointReceiveId = $point->id;
                  }
              @endphp
              <tr>
                <th>招待者ID</th>
                <td><a href="{{ route('admin.users.show', ['user' => $inviteCodeHistory->inviteCode->user_id]) }}">{{ $inviteCodeHistory->inviteCode->user_id }}</a></td>
              </tr>
              <tr>
                <th>招待コード</th>
                <td>{{ $inviteCodeHistory->inviteCode->code }}</td>
              </tr>
              <tr>
                <th>利用者ID</th>
                <td><a href="{{ route('admin.users.show', ['user' => $inviteCodeHistory->receive_user_id]) }}">{{ $inviteCodeHistory->receive_user_id }}</a></td>
              </tr>
              <tr>
                <th>利用者会員登録日時</th>
                <td>{{ Carbon\Carbon::parse($inviteCodeHistory->user->created_at)->format('Y/m/d H:i') }}</td>
              </tr>
              <tr>
                <th>招待コード入力日時</th>
                <td>{{ Carbon\Carbon::parse($inviteCodeHistory->created_at)->format('Y/m/d H:i') }}</td>
              </tr>
              <tr>
                <th>適用対象予約ID</th>
                @if ($inviteCodeHistory->order)
                  @if ($inviteCodeHistory->order->type == App\Enums\OrderType::NOMINATION)
                    <td><a href="{{ route('admin.orders.order_nominee', ['order' => $inviteCodeHistory->order_id]) }}">{{ $inviteCodeHistory->order_id }}</a></td>
                  @else
                    <td><a href="{{ route('admin.orders.call', ['order' => $inviteCodeHistory->order_id]) }}">{{ $inviteCodeHistory->order_id }}</a></td>
                  @endif
                @else
                <td>-</td>
                @endif
              </tr>
              <tr>
                <th>適用対象予約の予約ステータス</th>
                @if ($inviteCodeHistory->order)
                  @if ($inviteCodeHistory->order->payment_status)
                  <td>{{ App\Enums\OrderPaymentStatus::getDescription($inviteCodeHistory->order->payment_status) }}</td>
                  @else
                  <td>{{ App\Enums\OrderStatus::getDescription($inviteCodeHistory->order->status) }}</td>
                  @endif
                @else
                <td>-</td>
                @endif
              </tr>
              <tr>
                <th>招待者へのポイント付与</th>
                <td>{{ App\Enums\InviteCodeHistoryStatus::getDescription($inviteCodeHistory->status) }}</td>
              </tr>
              <tr>
                <th>招待者の購入ID</th>
                <td>{{ $pointInviteId }}</td>
              </tr>
              <tr>
                <th>利用者へのポイント付与</th>
                <td>{{ App\Enums\InviteCodeHistoryStatus::getDescription($inviteCodeHistory->status) }}</td>
              </tr>
              <tr>
                <th>利用者の購入ID</th>
                <td>{{ $pointReceiveId }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
