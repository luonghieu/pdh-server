@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading" data-original-title>
          <h2><span class="break"></span>コール/コール内指名_予約詳細</h2>
        </div>
        <div class="panel-body">
          <div class="display-title">
            <p><b>予約ID:</b> {{ $order->id }}</p>
          </div>
        </div>
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body">
          <div class="col-lg-12">
          </div>
          <div class="clearfix"></div>
          <div class="info-table col-lg-6">
            <table class="table table-bordered">
              <!--  table-striped -->
              <tr>
                <th>予約者ID</th>
                <td>
                  <a href="{{ route('admin.users.show', ['user' => $order->user->id]) }}">
                    {{ $order->user->id }}
                  </a>
                </td>
              </tr>
              <tr>
                <th>予約者名</th>
                <td>{{ $order->user->fullname }}</td>
              </tr>
              <tr>
                <th>予約区分</th>
                <td>{{ App\Enums\OrderType::getDescription($order->type) }}</td>
              </tr>
              <tr>
                <th>ルームID</th>
                <td>
                  @if ($order->room)
                  <a href="{{ App\Enums\OrderStatus::ACTIVE == $order->status ? route('admin.rooms.messages_by_room', ['room' => $order->room->id]) : '#' }}">
                    {{ $order->room->id }}
                  </a>
                  @endif
                </td>
              </tr>
              <tr>
                <th>キャストを呼ぶ場所</th>
                <td>{{ $order->address }}</td>
              </tr>
              <tr>
                <th>キャストとの合流時間</th>
                <td>{{ Carbon\Carbon::parse($order->start_time)->format('Y/m/d H:i') }}</td>
              </tr>
              <tr>
                <th>キャストの呼ぶ人数</th>
                <td>{{ $order->total_cast }}人</td>
              </tr>
              <tr>
                <th>キャストを呼ぶ時間</th>
                <td>{{ $order->duration }}時間</td>
              </tr>
              <tr>
                <th>キャストクラス</th>
                <td>{{ $order->castClass->name }}</td>
              </tr>
              <tr>
                <th>条件指定</th>
                <td>
                  @foreach($order->tags as $tag)
                      #{{ $tag->name }}
                  @endforeach
                </td>
              </tr>
              <tr>
                <th>指名キャスト</th>
                @if ($order->nominees->count() >= 1)
                  @if ($order->nominees->count() > 1)
                  <td><a href="{{ route('admin.orders.nominees', ['order' => $order->id]) }}">{{ $order->nominees->count().'名' }}</a></td>
                  @else
                  <td><a href="{{ route('admin.users.show', ['user' => $order->nominees[0]->id]) }}">{{ $order->nominees[0]->id }}</a></td>
                  @endif
                @else
                <td></td>
                @endif
              </tr>
              <tr>
                <th>応募中キャスト</th>
                @if ($order->candidates->count() >= 1)
                  @if ($order->candidates->count() > 1)
                  <td><a href="{{ route('admin.orders.candidates', ['order' => $order->id]) }}">{{ $order->candidates->count().'名' }}</a></td>
                  @else
                  <td><a href="{{ route('admin.users.show', ['user' => $order->candidates[0]->id]) }}">{{ $order->candidates[0]->id }}</a></td>
                  @endif
                @else
                <td></td>
                @endif
              </tr>
              <tr>
                <th>　予定合計ポイント</th>
                <td>{{ number_format($order->total_point) }}P</td>
              </tr>
              <tr>
                <th>ステータス</th>
                <td>
                  @if (App\Enums\OrderStatus::CANCELED == $order->status)
                    @if ($order->cancel_fee_percent == 0)
                    <span>確定後キャンセル (キャンセル料なし)</span>
                    @else
                    <span>確定後キャンセル (キャンセル料あり)</span>
                    @endif
                  @else
                    @if ($order->payment_status != null)
                    {{ App\Enums\OrderPaymentStatus::getDescription($order->payment_status) }}
                    @else
                    {{ App\Enums\OrderStatus::getDescription($order->status) }}
                    @endif
                  @endif
                </td>
              </tr>
              <tr>
                <th>予約発生時刻</th>
                <td>{{ Carbon\Carbon::parse($order->created_at)->format('Y/m/d H:i') }}</td>
              </tr>
              @if ($order->status == App\Enums\OrderStatus::PROCESSING)
              <tr>
                <th>マッチングした1
                キャスト</th>
                <td><a href="{{ route('admin.orders.casts_matching', ['order' => $order->id]) }}">{{ $order->casts->count().'人' }}</a></td>
              </tr>
              @endif
              @if ($order->point && $order->point->is_pay)
              <tr>
                <th>実績合計ポイント</th>
                <td>{{ number_format($order->casts()->sum('total_point')) }}P</td>
              </tr>
              <tr>
                <th>ポイント決済</th>
                <td>{{ ($order->point->status == App\Enums\Status::ACTIVE) ? '正常に完了しました':'エラー' }}</td>
              </tr>
              @endif
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
