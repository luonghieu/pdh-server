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
          <div class="info-table col-lg-10">
            <table class="table table-bordered change-width-th">
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
                  @if ($order->status >= App\Enums\OrderStatus::ACTIVE && $order->room)
                  <a href="{{ route('admin.rooms.messages_by_room', ['room' => $order->room->id]) }}">
                    {{ $order->room->id }}
                  </a>
                  @else
                  <span>-</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>キャストを呼ぶ場所</th>
                <td>{{ $order->address }}</td>
              </tr>
              <tr>
                <th>キャストとの合流時間</th>
                <td>{{ Carbon\Carbon::parse($order->date.' '.$order->start_time)->format('Y/m/d H:i') }}</td>
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
                <td>
                  @if (in_array($order->status, [App\Enums\OrderStatus::ACTIVE, App\Enums\OrderStatus::PROCESSING, App\Enums\OrderStatus::DONE]))
                  @php
                    $tempPoint = 0;
                    foreach ($order->casts as $cast) {
                      $tempPoint+=$cast->pivot->temp_point;
                    }
                  @endphp
                  {{ number_format($tempPoint).'P' }}
                  @else
                  {{ number_format($order->temp_point).'P' }}
                  @endif
              </tr>
              <tr>
                <th>ステータス</th>
                <td class="wrap-status">
                  @if ($order->payment_status != null)
                  {{ App\Enums\OrderPaymentStatus::getDescription($order->payment_status) }}
                  @else
                    @if (App\Enums\OrderStatus::DENIED == $order->status || App\Enums\OrderStatus::CANCELED == $order->status)
                      @if ($order->type == App\Enums\OrderType::NOMINATION && (count($order->nominees) > 0 ? empty($order->nominees[0]->pivot->accepted_at) : false))
                      <span>提案キャンセル</span>
                      @else
                        @if ($order->cancel_fee_percent == 0)
                        <span>確定後キャンセル (キャンセル料なし)</span>
                        @else
                        <span>確定後キャンセル (キャンセル料あり)</span>
                        @endif
                      @endif
                    @else
                    {{ App\Enums\OrderStatus::getDescription($order->status) }}
                    @endif
                  @endif
                  @if (App\Enums\OrderPaymentStatus::EDIT_REQUESTING == $order->payment_status)
                  <button class="change-time payment-request btn-order-call" data-toggle="modal" data-target="#payment-request">ステータスを売上申請待ちに切り替える</button>
                  @endif
                </td>
              </tr>
              <tr>
                <th>予約発生時刻</th>
                <td>{{ Carbon\Carbon::parse($order->created_at)->format('Y/m/d H:i') }}</td>
              </tr>
              @if ($order->status >= App\Enums\OrderStatus::PROCESSING)
              <tr>
                <th>マッチングしたキャスト</th>
                <td>
                  @if ($order->casts && $order->casts->count() > 0)
                  <a href="{{ route('admin.orders.casts_matching', ['order' => $order->id]) }}">{{ $order->casts->count().'人' }}</a>
                  @else
                  <span>0</span>
                  @endif
                </td>
              </tr>
              @endif
              @if ($order->status >= App\Enums\OrderStatus::DONE)
                <tr>
                  <th>実績合計ポイント</th>
                  <td>
                    @if ($order->payment_status == App\Enums\OrderPaymentStatus::REQUESTING || $order->status == App\Enums\OrderStatus::CANCELED)
                    {{ number_format($order->total_point).'P' }}
                    @else
                      @php
                        if (count($order->casts) > 0) {
                          $tempPoint = 0;
                          foreach ($order->casts as $cast) {
                            $tempPoint+=$cast->pivot->total_point;
                          }
                          $tempPoint = number_format($tempPoint).'P';
                        } else {
                          $tempPoint = '-';
                        }
                      @endphp
                    {{ $tempPoint }}
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>ポイント決済</th>
                  <td>
                  @if ($order->payment_status == App\Enums\OrderPaymentStatus::PAYMENT_FINISHED)
                  正常に完了しました
                  @else
                  エラー
                  @endif
                  </td>
                </tr>
              @endif
            </table>
            <div class="modal fade" id="payment-request" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <p>ステータスを「売上申請待ち」に変更しますか？</p>
                  </div>
                  <div class="modal-footer">
                    <form action="{{ route('admin.orders.change_payment_request_status',['order' => $order->id]) }}" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                      <input type="hidden" name="page" value="order_call">
                      <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                      <button type="submit" class="btn btn-accept">はい</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
