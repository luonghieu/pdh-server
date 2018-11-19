@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.orders.index')}}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前,予約ID" name="search" value="{{request()->search}}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn-search"></button>
              <input type="hidden" name="limit" value="{{ request()->limit }}" />
              @include('admin.orders.request_sort')
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
                @include('admin.orders.request_sort')
              </div>
            </div>
          </form>
        </div>
        <div class="btn-delete-order">
          <button data-toggle="modal" data-target="#deleteOrder">チェックした予約を無効する</button>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          @php
            $request = [
              'page' => request()->page,
              'limit' => request()->limit,
              'search' => request()->search,
              'from_date' => request()->from_date,
              'to_date' => request()->to_date,
           ];
          @endphp
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th></th>
                <th>No.</th>
                <th class="sorting{{ (request()->user_id) ? '_' . request()->user_id: '' }}">
                  <a href="{{ route('admin.orders.index',
                    array_merge($request, ['user_id' => (request()->user_id == 'asc') ? 'desc' : 'asc',])
                    ) }}">予約者ID
                   </a>
                </th>
                <th>予約者名</th>
                <th class="sorting{{ (request()->id) ? '_' . request()->id: '' }}">
                  <a href="{{ route('admin.orders.index',
                    array_merge($request, ['id' => (request()->id == 'asc') ? 'desc' : 'asc',])
                    ) }}">予約ID
                   </a>
                </th>
                <th class="sorting{{ (request()->type) ? '_' . request()->type: '' }}">
                  <a href="{{ route('admin.orders.index',
                    array_merge($request, ['type' => (request()->type == 'asc') ? 'desc' : 'asc',])
                    ) }}">予約区分
                   </a>
                </th>
                <th class="sorting{{ (request()->address) ? '_' . request()->address: '' }}">
                  <a href="{{ route('admin.orders.index',
                    array_merge($request, ['address' => (request()->address == 'asc') ? 'desc' : 'asc',])
                    ) }}">希望エリア
                   </a>
                </th>
                <th class="sorting{{ (request()->created_at) ? '_' . request()->created_at: '' }}">
                  <a href="{{ route('admin.orders.index',
                    array_merge($request, ['created_at' => (request()->created_at == 'asc') ? 'desc' : 'asc',])
                    ) }}">予約発生時刻
                   </a>
                </th>
                <th class="sorting{{ (request()->date && request()->start_time) ? '_' . request()->date . ' ' . request()->start_time: '' }}">
                  <a href="{{ route('admin.orders.index',
                    array_merge($request, ['date' => (request()->date == 'asc') ? 'desc' : 'asc',
                     'start_time' => (request()->start_time == 'asc') ? 'desc' : 'asc',])
                    ) }}">予定開始日時
                   </a>
                </th>
                <th>希望人数</th>
                <th>指名キャスト</th>
                <th>応募キャスト</th>
                <th class="sorting{{ (request()->status) ? '_' . request()->status : '' }}">
                  <a href="{{ route('admin.orders.index',
                    array_merge($request, ['status' => (request()->status == 'asc') ? 'desc' : 'asc',])
                    ) }}">ステータス
                   </a>
                </th>
                <th class="sorting{{ (request()->alert) ? '_' . request()->alert : '' }}">
                  <a href="{{ route('admin.orders.index',
                    array_merge($request, ['alert' => (request()->alert == 'asc') ? 'desc' : 'asc',])
                    ) }}">アラート
                   </a>
                </th>
                <th class="column-th-btn"></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($orders->count()))
                <tr>
                  <td colspan="14">{{ trans('messages.results_not_found') }}</td>
                </tr>
              @else
                @foreach ($orders as $key => $order)
                <tr>
                  <td class="select-checkbox">
                    @if (!$order->deleted_at)
                    <input type="checkbox" class="verify-checkboxs" value="{{ $order->id }}">
                    @endif
                  </td>
                  <td>{{ $orders->firstItem() + $key }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $order->user_id]) }}">{{ $order->user_id }}</a></td>
                  <td>{{ $order->user ? $order->user->nickname : '' }}</td>
                  <td>{{ $order->id }}</td>
                  <td>{{ App\Enums\OrderType::getDescription($order->type) }}</td>
                  <td class="address">{{ $order->address }}</td>
                  <td>{{ Carbon\Carbon::parse($order->created_at)->format('Y/m/d H:i') }}</td>
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
                  @if (App\Enums\OrderType::NOMINATION == $order->type)
                  <td>-</td>
                  @else
                    @if ($order->candidates->count() > 1)
                      <td><a href="{{ route('admin.orders.candidates', ['order' => $order->id]) }}">{{ $order->candidates->count() }} 名</a></td>
                    @else
                    <td><a href="{{ $order->candidates->first() ? route('admin.users.show', ['user' => $order->candidates->first()->id]) : '#' }}">{{ $order->candidates->first() ? $order->candidates->first()->id : "" }}</a></td>
                    @endif
                  @endif
                  <td>
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
                  </td>
                  <td>
                    @php
                      $startTime = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->date . ' ' . $order->start_time);
                      $endDay = $startTime->copy()->addHours($order->duration);
                      $now = Carbon\Carbon::now();
                    @endphp
                    @if (($order->status == App\Enums\OrderStatus::PROCESSING) && ($endDay < $now))
                      <span class="warning-order">予定時刻が過ぎています</span>
                    @endif
                    @if(($order->status == App\Enums\OrderStatus::ACTIVE) && ($startTime < $now))
                      <span class="warning-order">スタートボタンが押されていません</span>
                    @endif
                    @if($order->payment_status == App\Enums\OrderPaymentStatus::PAYMENT_FAILED)
                      <span class="warning-order">決済エラーが発生しました</span>
                    @endif
                  </td>
                  @if ($order->type == App\Enums\OrderType::NOMINATION)
                    <td><a href="{{ route('admin.orders.order_nominee', ['order' => $order->id]) }}" class="btn btn-detail">詳細</a></td>
                  @else
                    <td><a href="{{ route('admin.orders.call', ['order' => $order->id]) }}" class="btn btn-detail">詳細</a></td>
                  @endif
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <div class="modal fade" id="deleteOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>チェックした予約を無効しますか？</p>
              </div>
              <form action="{{ route('admin.orders.delete') }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <input type="hidden" id="order_ids" name="order_ids" value="{{ old('order_ids') }}" />
                <div class="modal-footer">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept del-order">はい</button>
                </div>
              </form>
            </div>
          </div>
        </div>
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
@section('admin.js')
  <script type="text/javascript">
    $('.del-order').on('click', function() {
      var order_ids = [];
      $('.verify-checkboxs:checked').each(function() {
        order_ids.push(this.value);
      });

      $('#order_ids').val(order_ids.join(','));

      return true;
    });
  </script>
@stop
