@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading" data-original-title>
          <h2><span class="break"></span>指名予約_予約詳細</h2>
        </div>
        <div class="panel-body">
          <div class="display-room-id">
            <p><b>予約ID:</b> {{ $order->id }}</p>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="clearfix"></div>
        <div class="panel-body">
        @include('admin.partials.notification')
          <div class="info-table col-lg-10">
            <table class="table table-bordered change-width-th">
              <!--  table-striped -->
              <tr>
                <th>予約者ID</th>
                <td>
                  @if ($order->user)
                  <a href="{{ route('admin.users.show', ['user' => $order->user->id]) }}">
                    {{ $order->user->id }}
                  </a>
                  @else
                  <span>-</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>予約者名</th>
                <td>{{ $order->user ? $order->user->nickname : '' }}</td>
              </tr>
              <tr>
                <th>指名キャスト名</th>
                <td>{{ (count($order->castOrder) > 0) ? $order->castOrder[0]->nickname : '' }}</td>
              </tr>
              <tr>
                <th>予約区分</th>
                <td>{{ App\Enums\OrderType::getDescription($order->type) }}</td>
              </tr>
              <tr>
                <th>ルームID</th>
                <td>
                  @if (App\Enums\OrderStatus::ACTIVE <= $order->status && $order->room)
                  <a href="{{ $order->room ? route('admin.rooms.messages_by_room', ['room' => $order->room->id]) : '#' }}">
                    {{ $order->room->id }}
                  </a>
                  @endif
                  @if (App\Enums\OrderStatus::OPEN == $order->status)
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
                <td>{{ Carbon\Carbon::parse($order->date .' '. $order->start_time)->format('Y/m/d H:i') }}</td>
              </tr>
              <tr>
                <th>キャストを呼ぶ時間</th>
                <td>{{ $order->duration }}時間</td>
              </tr>
              <tr>
                <th>予定合計ポイント</th>
                <td>
                  @if (in_array($order->status, [App\Enums\OrderStatus::ACTIVE, App\Enums\OrderStatus::PROCESSING, App\Enums\OrderStatus::DONE]))
                  {{ count($order->casts) > 0 ? number_format($order->casts[0]->pivot->temp_point).'P' : '0P' }}
                  @else
                  {{ number_format($order->temp_point).'P' }}
                  @endif
                </td>
              </tr>
              <tr>
                <th>ステータス</th>
                <td class="wrap-status">
                  @if ($order->payment_status != null)
                  <span>{{ App\Enums\OrderPaymentStatus::getDescription($order->payment_status) }}</span>
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
                    <span>{{ App\Enums\OrderStatus::getDescription($order->status) }}</span>
                    @endif
                  @endif
                  @if (App\Enums\OrderPaymentStatus::EDIT_REQUESTING == $order->payment_status)
                  <button class="change-time payment-request btn-nominee" data-toggle="modal" data-target="#payment-request">ステータスを売上申請待ちに切り替える</button>
                  <button class="change-time btn-pay-point" data-toggle="modal" data-target="#pay-point">ステータスをポイント決済完了に切り替える</button>
                  @endif
                </td>
              </tr>
              <tr>
                <th>予約発生時刻</th>
                <td>{{ Carbon\Carbon::parse($order->created_at)->format('Y/m/d H:i') }}</td>
              </tr>
              @if (App\Enums\OrderStatus::PROCESSING <= $order->status)
              <tr>
                <th>合流時刻</th>
                <td class="wrap-status">
                  {{ (count($order->casts) > 0) ? Carbon\Carbon::parse($order->casts[0]->pivot->started_at)->format('Y/m/d H:i') : '' }}
                  @if (!in_array($order->payment_status, [App\Enums\OrderPaymentStatus::PAYMENT_FINISHED, App\Enums\OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED]))
                  <button class="change-time order-nominee-started-time" data-toggle="modal" data-target="#order-nominee-started-time">合流時刻を修正する</button>
                  @endif
                </td>
              </tr>
              <tr>
                <th>解散時刻</th>
                <td class="wrap-status">
                  @if(count($order->casts) > 0 && $order->casts[0]->pivot->stopped_at)
                    {{ Carbon\Carbon::parse($order->casts[0]->pivot->stopped_at)->format('Y/m/d H:i') }}
                    @if (!in_array($order->payment_status, [App\Enums\OrderPaymentStatus::PAYMENT_FINISHED, App\Enums\OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED]))
                    <button class="change-time order-nominee-stopped-time" data-toggle="modal" data-target="#order-nominee-stopped-time">解散時刻を修正する</button>
                    @endif
                  @endif
                </td>
              </tr>
              <tr>
                <th>延長時間</th>
                <td>{{ (count($order->casts) > 0) ? ($order->casts[0]->pivot->extra_time != null ? $order->casts[0]->pivot->extra_time.'分' : '0分') : '' }}</td>
              </tr>
              <tr>
                <th>延長料</th>
                <td>{{ (count($order->casts) > 0) ? number_format($order->casts[0]->pivot->extra_point).'P' : '' }}</td>
              </tr>
              <tr>
                <th>指名料</th>
                <td>{{ (count($order->casts) > 0) ? number_format($order->casts[0]->pivot->fee_point).'P' : '' }}</td>
              </tr>
              <tr>
                <th>深夜手当</th>
                <td>{{ (count($order->casts) > 0) ? number_format($order->casts[0]->pivot->night_time).'P' : '' }}</td>
              </tr>
              <tr>
                <th>実績合計ポイント</th>
                <td>
                  @if ($order->status == App\Enums\OrderStatus::PROCESSING)
                  {{ count($order->casts) > 0 ? number_format($order->casts[0]->pivot->temp_point).'P' : '0P' }}
                  @endif
                  @if ($order->status >= App\Enums\OrderStatus::DONE)
                    @if ($order->payment_status == App\Enums\OrderPaymentStatus::REQUESTING || $order->status == App\Enums\OrderStatus::CANCELED)
                    {{ number_format($order->total_point).'P' }}
                    @else
                    {{ count($order->casts) > 0 ? number_format($order->casts[0]->pivot->total_point).'P' : '0P' }}
                    @endif
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
                      <input type="hidden" name="page" value="order_nominee">
                      <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                      <button type="submit" class="btn btn-accept">はい</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="pay-point" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <p>ステータスを「ポイント決済」に変更しますか？</p>
                    <p>「はい」をタップすると、決済が実行されます。</p>
                  </div>
                  <div class="modal-footer">
                    <form action="{{ route('admin.orders.point_settlement',['order' => $order->id]) }}" method="POST">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                      <input type="hidden" name="page" value="order_nominee">
                      <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                      <button type="submit" class="btn btn-accept">はい</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="order-nominee-started-time" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <p>合流時刻を修正する</p>
                    <div>
                      <form action="{{ route('admin.orders.change_start_time_order_nominee') }}" method="POST">
                      {{ csrf_field() }}
                      {{ method_field('PUT') }}
                        <input type="text" class="date-picker input-update-date-order input-search" name="start_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ count($order->casts) > 0 ? Carbon\Carbon::parse($order->casts[0]->pivot->started_at)->format('Y/m/d') : '' }}" placeholder="yyyy/mm/dd" />
                        <select name="start_time_hour">
                          @for ($i = 0; $i < 24; $i++)
                          <option value="{{ $i }}" {{ Carbon\Carbon::parse((count($order->casts) > 0) ? $order->casts[0]->pivot->started_at : '')->format('H') == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                        <span>:</span>
                        <select name="start_time_minute">
                          @for ($i = 0; $i < 60; $i++)
                          <option value="{{ $i }}" {{ Carbon\Carbon::parse((count($order->casts) > 0) ? $order->casts[0]->pivot->started_at : '')->format('i') == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                        <input type="hidden" name="orderId" value="{{ $order->id }}">
                        <input type="hidden" name="cast_id" value="{{ (count($order->casts) > 0) ? $order->casts[0]->id : '' }}">
                    </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                      <button type="submit" class="btn btn-accept">合流時刻を修正する</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade" id="order-nominee-stopped-time" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <p>解散時刻を修正する</p>
                    <div>
                      <form action="{{ route('admin.orders.change_stop_time_order_nominee') }}" method="POST">
                      {{ csrf_field() }}
                      {{ method_field('PUT') }}
                        <input type="text" class="date-picker input-update-date-order input-search" name="stop_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ count($order->casts) > 0 ? Carbon\Carbon::parse($order->casts[0]->pivot->stopped_at)->format('Y/m/d') : '' }}" placeholder="yyyy/mm/dd" />
                        <select name="stop_time_hour">
                          @for ($i = 0; $i < 24; $i++)
                          <option value="{{ $i }}" {{ Carbon\Carbon::parse((count($order->casts) > 0) ? $order->casts[0]->pivot->stopped_at : '')->format('H') == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                        <span>:</span>
                        <select name="stop_time_minute">
                          @for ($i = 0; $i < 60; $i++)
                          <option value="{{ $i }}" {{ Carbon\Carbon::parse((count($order->casts) > 0) ? $order->casts[0]->pivot->stopped_at : '')->format('i') == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                        <input type="hidden" name="orderId" value="{{ $order->id }}">
                        <input type="hidden" name="cast_id" value="{{ (count($order->casts) > 0) ? $order->casts[0]->id : '' }}">
                    </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                      <button type="submit" class="btn btn-accept">解散時刻を修正する</button>
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
