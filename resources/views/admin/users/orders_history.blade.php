@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab',compact('user'))
        <div class="clearfix"></div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>ユーザーID</th>
                <th>ニックネーム</th>
                <th>予約ID</th>
                <th>合流日時</th>
                <th>利用ポイント</th>
                <th>区分</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($orders as $key => $order)
              <tr>
                <td>{{ $orders->firstItem() + $key }}</td>
                <td><a href="{{ route('admin.users.show', ['user' => $user->id]) }}">{{ $order->user_id }}</a></td>
                <td>{{ $order->user->nickname }}</td>
                <td><a href="{{ route('admin.orders.call', ['order' => $order->id]) }}">{{ $order->id }}</a></td>
                <td>{{ Carbon\Carbon::parse($order->date)->format('Y/m/d') }} {{ Carbon\Carbon::parse($order->start_time)->format('H:i') }}</td>
                <td>{{ ($order->total_point < $order->discount_point) ? 0 : number_format($order->total_point - $order->discount_point) }}P</td>
                <td>{{ App\Enums\OrderType::getDescription($order->type) }}</td>
                @if ($order->type == App\Enums\OrderType::NOMINATION)
                  <td><a href="{{ route('admin.orders.order_nominee', ['order' => $order->id]) }}" class="btn btn-detail">詳細</a></td>
                @else
                  <td><a href="{{ route('admin.orders.call', ['order' => $order->id]) }}" class="btn btn-detail">詳細</a></td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
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
