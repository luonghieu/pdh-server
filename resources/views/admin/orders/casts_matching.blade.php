@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="display-title">
            <p><b>予約ID:</b> {{ $order->id }} の　マッチングしたキャスト一覧</p>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="clearfix"></div>
        <div class="panel-body">
        @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable info-cast-matching">
            <thead>
              <tr>
                <th>No.</th>
                <th>ユーザーID</th>
                <th>ニックネーム</th>
                <th>応募時間</th>
              </tr>
            </thead>
            <tbody>
              @php
                $i = 1;
              @endphp
              @foreach ($casts as $cast)
              <tr>
                <td>{{ $i++ }}</td>
                <td><a href="{{ route('admin.users.show', ['user' => $cast->id]) }}">{{ $cast->id }}</a></td>
                <td>
                  @if ($cast->provider == App\Enums\ProviderType::EMAIL)
                  <span class="color-error">★</span>
                  @endif
                  {{ $cast->nickname }}
                </td>
                <td>{{ Carbon\Carbon::parse($cast->pivot->created_at)->format('Y/m/d H:i') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div class="clearfix"></div>
          @foreach ($casts as $cast)
          <div class="info-table col-lg-8">
            <p class="info-cast-detail">ユーザーID: {{ $cast->id }}　{{ $cast->firstname }} さんの稼働実績 &nbsp&nbsp&nbsp&nbsp
              @if (isset($paymentRequests[$cast->id]) && in_array($paymentRequests[$cast->id]['status'], [App\Enums\PaymentRequestStatus::REQUESTED, App\Enums\PaymentRequestStatus::UPDATED]))
              <span>売上申請完了</span>
              @endif
            </p>
            <table class="table table-bordered">
              <!--  table-striped -->
              <tr>
                <th>合流時刻</th>
                <td class="wrap-status">
                  {{ $cast->pivot->started_at ? Carbon\Carbon::parse($cast->pivot->started_at)->format('Y/m/d H:i'): '' }}
                  @if (($order->status == App\Enums\OrderStatus::DONE && (!in_array($order->payment_status, [App\Enums\OrderPaymentStatus::PAYMENT_FINISHED, App\Enums\OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED]))) || ($order->status == App\Enums\OrderStatus::PROCESSING && $cast->pivot->status == App\Enums\CastOrderStatus::DONE))
                  <button class="change-time start-time" data-toggle="modal" data-target="#start-time-{{ $cast->id }}">合流時刻を修正する</button>
                  @endif
                </td>
              </tr>
              <tr>
                <th>解散時刻</th>
                <td class="wrap-status">
                  @if($cast->pivot->stopped_at)
                    {{ Carbon\Carbon::parse($cast->pivot->stopped_at)->format('Y/m/d H:i') }}
                    @if (($order->status == App\Enums\OrderStatus::DONE && (!in_array($order->payment_status, [App\Enums\OrderPaymentStatus::CANCEL_FEE_PAYMENT_FINISHED, App\Enums\OrderPaymentStatus::PAYMENT_FINISHED])))|| ($order->status == App\Enums\OrderStatus::PROCESSING && $cast->pivot->status == \App\Enums\CastOrderStatus::DONE))
                    <button class="change-time stopped-time" data-toggle="modal" data-target="#stopped-time-{{ $cast->id }}">解散時刻を修正する</button>
                    @endif
                  @endif
                </td>
              </tr>
              <tr>
                <th>延長時間</th>
                <td>{{ $cast->pivot->extra_time }}分</td>
              </tr>
              <tr>
                <th>指名料</th>
                <td>{{ number_format($cast->pivot->fee_point) }}P</td>
              </tr>
              <tr>
                <th>深夜手当</th>
                <td>{{ number_format($cast->pivot->allowance_point) }}P</td>
              </tr>
              <tr>
                <th>実績合計ポイント</th>
                <td>
                  @if ($order->status == App\Enums\OrderStatus::PROCESSING)
                    @if ($cast->pivot->status == \App\Enums\CastOrderStatus::DONE)
                    {{ (number_format($cast->pivot->total_point) ?? '0').'P' }}
                    @else
                    {{ (number_format($cast->pivot->temp_point) ?? '0').'P' }}
                    @endif
                  @endif
                  @if ($order->status >= App\Enums\OrderStatus::DONE)
                  {{ number_format($cast->pivot->total_point) }}P
                  @endif
                </td>
              </tr>
            </table>
          </div>
          <div class="modal fade" id="start-time-{{ $cast->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-body">
                  <p>合流時刻を修正する</p>
                  <div>
                    <form action="{{ route('admin.orders.change_start_time_order_call') }}" method="post">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                      <input type="text" class="date-picker input-update-date-order input-search" name="start_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ Carbon\Carbon::parse($cast->pivot->started_at)->format('Y/m/d') }}" placeholder="yyyy/mm/dd" />
                      <select name="start_time_hour">
                        @for ($i = 0; $i < 24; $i++)
                        <option value="{{ $i }}" {{ Carbon\Carbon::parse($cast->pivot->started_at)->format('H') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                      </select>
                      <span>:</span>
                      <select name="start_time_minute">
                        @for ($i = 0; $i < 60; $i++)
                        <option value="{{ $i }}" {{ Carbon\Carbon::parse($cast->pivot->started_at)->format('i') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                      </select>
                      <input type="hidden" name="order_id" value="{{ $order->id }}">
                      <input type="hidden" name="cast_id" value="{{ $cast->id }}">
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
          <div class="modal fade" id="stopped-time-{{ $cast->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-body">
                  <p>解散時刻を修正する</p>
                  <div>
                    <form action="{{ route('admin.orders.change_stopped_time_order_call') }}" method="post">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                      <input type="text" class="date-picker input-update-date-order input-search" name="stop_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ Carbon\Carbon::parse($cast->pivot->stopped_at)->format('Y/m/d') }}" placeholder="yyyy/mm/dd" />
                      <select name="stop_time_hour">
                        @for ($i = 0; $i < 24; $i++)
                        <option value="{{ $i }}" {{ Carbon\Carbon::parse($cast->pivot->stopped_at)->format('H') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                      </select>
                      <span>:</span>
                      <select name="stop_time_minute">
                        @for ($i = 0; $i < 60; $i++)
                        <option value="{{ $i }}" {{ Carbon\Carbon::parse($cast->pivot->stopped_at)->format('i') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                      </select>
                      <input type="hidden" name="order_id" value="{{ $order->id }}">
                      <input type="hidden" name="cast_id" value="{{ $cast->id }}">
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-accept">延長時間を修正する</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
