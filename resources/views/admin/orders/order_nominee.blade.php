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
          <div class="col-lg-10">
            @if(in_array($order->status, [App\Enums\OrderStatus::OPEN, App\Enums\OrderStatus::ACTIVE]))
            <button type="button" data-toggle="modal" data-target="#btn-edit-order-nominee" class="btn btn-info pull-right">予約内容を変更する</button>
            @endif
            <form action="{{ route('admin.orders.order_nominee_edit', ['order' => $order->id]) }}" method="POST">
              <div class="modal fade" id="btn-submit-edit-order-nominee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-body">
                      <div class="title-modal">
                        <h2>本当に更新しますか？</h2>
                        <h2>「はい」を選択すると、ゲスト/キャストチャット</h2>
                        <h2>へ通知が送信されます</h2>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-canceled" data-dismiss="modal">いいえ</button>
                      <button type="submit" class="btn btn-accept">はい</button>
                    </div>
                  </div>
                </div>
              </div>

              {{ method_field('PUT') }}
              {{ csrf_field() }}
              <div class="modal fade" id="btn-edit-order-nominee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <div class="title-modal"><h2>予約を編集する</h2></div>
                    <div class="content-modal">
                      <p>① 予約者ID: {{ $order->id }}</p>
                      <p>② 予約者名: {{ $order->user->nickname }}</p>
                      <p>③ 指名キャスト名: {{ $order->nomineesWithTrashed->first()->nickname }}</p>
                      <div class="wrap-edit-start-time">
                        <p>④ キャストとの合流時間</p>
                        @php
                          $now = now();
                          $orderStartDate = \Carbon\Carbon::parse($order->date . ' ' . $order->start_time);
                          $dateInMonth = $orderStartDate->daysInMonth;
                        @endphp
                        <div class="start-date-time">
                          <select name="year" id="edit-year">
                            <option value="{{$orderStartDate->year}}"
                                    selected="selected">{{$orderStartDate->year}}年</option>
                            <option value="{{$orderStartDate->year+1}}">{{$orderStartDate->year+1}}年</option>
                          </select>
                          <select name="month" id="edit-month">
                            @for($i = 1; $i <= 12; $i++)
                              <option value="{{$i}}"
                                <?=($orderStartDate->month == $i) ? 'selected' : '' ?>
                                <?=($now->month > $i) ? ' disabled' : '' ?>
                              >{{$i}}月</option>
                            @endfor
                          </select>
                          <select name="day" id="edit-day">
                            @for($i = 1; $i <= $dateInMonth; $i++)
                              @php
                                if($i < 10) {
                                  $i = '0'.$i;
                                }
                              @endphp
                              <option value="{{$i}}"
                              <?=($orderStartDate->day == $i) ? 'selected' : '' ?>
                              <?=($orderStartDate->diffInMonths($now) == 0 && $now->day > $i) ? ' disabled' : '' ?>
                              >{{$i . '日'}}</option>
                            @endfor
                          </select>
                          <select name="hour" id="edit-hour">
                            @for($i = 0; $i < 24; $i++)
                              @php
                                if($i < 10) {
                                  $i = '0'.$i;
                                }
                              @endphp
                              <option value="{{$i}}"
                                <?=($orderStartDate->hour == $i) ? 'selected' : '' ?>
                                <?=($orderStartDate->diffInDays($now) == 0 && $now->hour > $i) ? ' disabled' : '' ?>
                              >{{ $i }}</option>
                            @endfor
                          </select>
                          :
                          <select name="minute" id="edit-minute">
                            @for($i = 0; $i < 60; $i++)
                              @php
                                if($i < 10) {
                                  $i = '0'.$i;
                                }
                              @endphp
                              <option value="{{$i}}"
                                <?=($orderStartDate->minute == $i) ? 'selected' : '' ?>
                                <?=($orderStartDate->diffInHours($now) == 0 && $now->minute > $i) ? ' disabled' : '' ?>
                              >{{$i}}</option>
                            @endfor
                          </select>
                        </div>
                      </div>
                      <div class="wrap-edit-duration-nominee">
                        <p>⑤ キャストを呼ぶ時間</p>
                        <div class="duration-nominee">
                          <select name="duration" id="edit-duration-nominee">
                            @for ($i = 1; $i < 11; $i++)
                              <option value="{{ $i }}" <?= ($order->duration == $i) ? 'selected' : '' ?> >{{ $i.'時間'
                              }}</option>
                            @endfor
                          </select>
                        </div>
                      </div>
                      <p>⑥ 予定合計ポイント: <span id="temp-point">{{ number_format($order->temp_point) . 'P' }}</span></p>
                    </div>
                  </div>
                  <div class="modal-footer">
                      <input type="hidden" id="order-start-date" name="order_start_date" value="{{
                      $orderStartDate->format('Y/m/d H:i')
                      }}">
                      <button type="button" data-toggle="modal" data-target="#btn-submit-edit-order-nominee"
                              class="btn btn-accept" data-dismiss="modal" onclick="onClickComfirm()">更新する
                      </button>
                  </div>
                </div>
              </div>
              </div>
            </form>
          </div>
        </div>
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
                <td>
                  @if (count($order->castOrder) > 0 && $order->castOrder[0]->provider == App\Enums\ProviderType::EMAIL)
                  <span class="color-error">★</span>
                  @endif
                  {{ (count($order->castOrder) > 0) ? $order->castOrder[0]->nickname : '' }}</td>
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
              @if($order->coupon_id)
                <tr>
                  <th>クーポン利用</th>
                  <td>{{ $order->coupon_name }}</td>
                </tr>
                <tr>
                  <th>クーポン割引額</th>
                  <td>{{ '-' . number_format($order->discount_point) .'P' }}</td>
                </tr>
              @endif
              <tr>
                <th>予定合計ポイント</th>
                <td>
                  @php
                    $tempPoint = 0;
                    if (in_array($order->status, [App\Enums\OrderStatus::ACTIVE, App\Enums\OrderStatus::PROCESSING, App\Enums\OrderStatus::DONE])) {
                      $tempPoint = count($order->casts) > 0 ? $order->casts[0]->pivot->temp_point : 0 ;

                      if($order->coupon_id) {
                        $tempPoint -= $order->discount_point;
                      }

                      if($tempPoint < 0 ) {
                        $tempPoint = 0;
                      }
                    } else {
                      $tempPoint = $order->temp_point;
                    }
                  @endphp

                  {{ number_format($tempPoint).'P' }}
                </td>
              </tr>
              <tr>
                <th>ステータス</th>
                <td class="wrap-status {{ (App\Enums\OrderStatus::CANCELED == $order->status &&
                ($order->payment_status == null || $order->payment_status !=
                App\Enums\OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED)) ? 'text-left' : '' }}">
                  @if ($order->payment_status != null)
                  <span>{{ App\Enums\OrderPaymentStatus::getDescription($order->payment_status) }}</span>
                  @else
                    @if (App\Enums\OrderStatus::DENIED == $order->status || App\Enums\OrderStatus::CANCELED == $order->status)
                      @if ($order->type == App\Enums\OrderType::NOMINATION && (count($order->nominees) > 0 ? empty($order->nominees[0]->pivot->accepted_at) : false))
                      <span>提案キャンセル</span>
                      @else
                        @if ($order->cancel_fee_percent == 0)
                        <span>確定後キャンセル (キャンセル料なし)</span>
                          @if ($order->payment_status != App\Enums\OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED)
                            <button id="set-status-to-active" class="change-time" data-toggle="modal"
                                    data-target="#order-nominee-update-status-to-active">
                              ステータスを
                              予約確定に切り替える
                            </button>
                          @endif
                        @else
                        <span>確定後キャンセル (キャンセル料あり)</span>
                          @if ($order->payment_status != App\Enums\OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED)
                            <button id="set-status-to-active" class="change-time" data-toggle="modal"
                                    data-target="#order-nominee-update-status-to-active">
                              ステータスを
                              予約確定に切り替える
                            </button>
                          @endif
                        @endif
                      @endif
                    @else
                    <span>{{ App\Enums\OrderStatus::getDescription($order->status) }}</span>
                    @endif
                  @endif
                  @if (App\Enums\OrderPaymentStatus::EDIT_REQUESTING == $order->payment_status)
                  <button class="change-time payment-request btn-nominee" data-toggle="modal" data-target="#payment-request">ステータスを売上申請待ちに切り替える</button>
                  @endif
                  @if (App\Enums\OrderPaymentStatus::REQUESTING == $order->payment_status || App\Enums\OrderPaymentStatus::EDIT_REQUESTING == $order->payment_status || App\Enums\OrderPaymentStatus::PAYMENT_FAILED == $order->payment_status)
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
                  @if ($order->status == App\Enums\OrderStatus::DONE && (!in_array($order->payment_status, [App\Enums\OrderPaymentStatus::PAYMENT_FINISHED, App\Enums\OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED])))
                  <button class="change-time order-nominee-started-time" data-toggle="modal" data-target="#order-nominee-started-time">合流時刻を修正する</button>
                  @endif
                </td>
              </tr>
              <tr>
                <th>解散時刻</th>
                <td class="wrap-status">
                  @if(count($order->casts) > 0 && $order->casts[0]->pivot->stopped_at)
                    {{ Carbon\Carbon::parse($order->casts[0]->pivot->stopped_at)->format('Y/m/d H:i') }}
                    @if (($order->status == App\Enums\OrderStatus::DONE && (!in_array($order->payment_status, [App\Enums\OrderPaymentStatus::PAYMENT_FINISHED, App\Enums\OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED])))|| ($order->casts[0]->pivot->stopped_at))
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
                <td>{{ (count($order->casts) > 0) ? number_format($order->casts[0]->pivot->allowance_point).'P' : '' }}</td>
              </tr>
              <tr>
                <th>実績合計ポイント</th>
                <td>
                  @php
                    $totalPoint = 0;
                    if ($order->status == App\Enums\OrderStatus::PROCESSING) {
                      $totalPoint = count($order->casts) > 0 ? $order->casts[0]->pivot->temp_point : 0;
                    }

                    if ($order->status >= App\Enums\OrderStatus::DONE) {
                      if ($order->payment_status) {
                        $totalPoint = $order->total_point;
                      } else {
                        $totalPoint = count($order->casts) > 0 ? $order->casts[0]->pivot->total_point : 0;
                      }
                    }

                    if($order->coupon_id) {
                      $totalPoint -= $order->discount_point;
                    }

                    if($totalPoint < 0 ) {
                      $totalPoint = 0;
                    }
                  @endphp

                  {{ number_format($totalPoint).'P' }}
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
            <div class="modal fade" id="order-nominee-update-status-to-active" tabindex="-1" role="dialog"
                 aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <p>ステータスを予約確定に切り替えますか？</p>
                    <div>
                      <form action="{{ route('admin.orders.update_status_to_active') }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $order->id }}">
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
@section('admin.js')
  <script type="text/javascript">
    let currentOrderStartDate = '<?= \Carbon\Carbon::parse($order->date . ' ' . $order->start_time)?>';
    const baseOrderStartDate = '<?= \Carbon\Carbon::parse($order->date . ' ' . $order->start_time)?>';
    const baseTempPoint = '<?= number_format($order->temp_point) ?>';
    const baseDuration = '<?= $order->duration ?>';
    let nomineeCost ='<?php echo $order->castOrder()->first()->pivot->cost ?>';
    let orderDuration = '<?= $order->duration ?>';
    let dayOfWeek = JSON.parse('<?= json_encode(dayOfWeek()) ?>');
    let clickComfirm = 0;
    function onClickComfirm() {
        clickComfirm = 1;
    }
  </script>
  <script src="/assets/admin/js/pages/edit_order_nominee.js"></script>
@stop

