@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.orders.index')}}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{request()->search}}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn-search"></button>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{route('admin.orders.index')}}" id="limit-page" method="GET">
            <div class="form-group">
              <label class="col-md-1 limit-page">表示件数：</label>
              <div class="col-md-1">
                <select id="select-limit" name="limit" class="form-control">
                  @foreach ([10, 20, 50, 100] as $limit)
                    <option value="{{ $limit }}" {{ request()->limit == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                  @endforeach
                </select>
                <input type="hidden" name="from_date" value="{{ request()->from_date }}" />
                <input type="hidden" name="to_date" value="{{ request()->to_date }}" />
                <input type="hidden" name="search" value="{{ request()->search }}" />
              </div>
            </div>
          </form>
        </div>
        <form action="{{ route('admin.orders.delete') }}" method="POST">
          {{ csrf_field() }}
          {{ method_field("DELETE") }}
          <div class="btn-delete-order">
            <button type="submit">
              <p>チェックした予約を削除する</p>
            </button>
          </div>
          <div class="panel-body">
            @include('admin.partials.notification')
            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th></th>
                  <th>No.</th>
                  <th>予約者ID</th>
                  <th>予約者名</th>
                  <th>予約ID</th>
                  <th>予約区分</th>
                  <th>希望エリア</th>
                  <th>予定開始日時</th>
                  <th>希望人数</th>
                  <th>指名キャスト</th>
                  <th>応募キャスト</th>
                  <th class="column-long-text">ステータス</th>
                  <th class="column-long-text">アラート</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($orders as $key => $order)
                <tr>
                  <td class="select-checkbox">
                    <input class="verify-checkboxs"
                      type="checkbox"
                      value="{{ $order->id }}"
                      name="order_ids[]">
                  </td>
                  <td>{{ $orders->firstItem() +$key }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $order->user->id]) }}">{{ $order->user ? $order->user->id : '' }}</a></td>
                  <td>{{ $order->user ? $order->user->nickname : '' }}</td>
                  <td>{{ $order->id }}</td>
                  <td>{{ App\Enums\OrderType::getDescription($order->type) }}</td>
                  <td>{{ $order->address }}</td>
                  <td>{{ Carbon\Carbon::parse($order->date)->format('Y/m/d') }} {{ Carbon\Carbon::parse($order->start_time)->format('H:i') }}</td>
                  <td>{{ $order->total_cast }} 名</td>
                  @if (App\Enums\OrderType::CALL == $order->type)
                  <td>-</td>
                  @else
                    @if ($order->nominees->count() > 1)
                      <td><a href="{{ route('admin.orders.nominees', ['order' => $order->id]) }}">{{ $order->nominees->count() }} 名</a></td>
                    @else
                    <td><a href="{{ $order->nominees->first() ? route('admin.users.show', ['user' => $order->nominees->first()->id]) : '#' }}">{{ $order->nominees->first() ? $order->nominees->first()->id : "" }}</a></td>
                    @endif
                  @endif
                  @if (App\Enums\OrderType::CALL == $order->type)
                    @if ($order->candidates->count() > 1)
                      <td><a href="{{ route('admin.orders.candidates', ['order' => $order->id]) }}">{{ $order->candidates->count() }} 名</a></td>
                    @else
                    <td><a href="{{ $order->candidates->first() ? route('admin.users.show', ['user' => $order->candidates->first()->id]) : '#' }}">{{ $order->candidates->first() ? $order->candidates->first()->id : "" }}</a></td>
                    @endif
                  @else
                  <td>-</td>
                  @endif
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
                  <td>
                    @php
                      $endDay = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->date . ' ' . $order->start_time)->addHours($order->duration);
                      $now = Carbon\Carbon::now();
                    @endphp
                    @if (($order->status == App\Enums\OrderStatus::PROCESSING) && ( $endDay < $now))
                      <span class="warning-order">予定時刻が過ぎています</span>
                    @endif
                  </td>
                  @if ($order->type == App\Enums\OrderType::NOMINATION)
                    <td><a href="{{ route('admin.orders.order_nominee', ['order' => $order->id]) }}" class="btn-detail">詳細</a></td>
                  @else
                    <td><a href="{{ route('admin.orders.call', ['order' => $order->id]) }}" class="btn-detail">詳細</a></td>
                  @endif
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </form>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($orders->total())
              全 {{ $orders->total() }}件中 {{ $orders->firstItem() }}~{{ $orders->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $orders->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
