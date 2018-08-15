@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.sales.index')}}" method="GET">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
              <select class="form-control search-point-type" name="search_point_type">
                @foreach ($pointTypes as $key => $pointType)
                  <option value="{{ $key }}" {{ request()->search_point_type == $key ? 'selected' : '' }}>{{ $pointType }}</option>
                @endforeach
              </select>
              <button type="submit" class="fa fa-search btn-search" name="submit" value="search"></button>
              <div class="export-csv">
                  <input type="hidden" name="limit" value="{{ request()->limit }}" />
                  <input type="hidden" name="is_export" value="1">
                  <button type="submit" class="export-btn" name="submit" value="export">CSV出力</button>
              </div>
            </form>
            <form class="navbar-form navbar-left form-search" action="{{route('admin.sales.index')}}" id="limit-page" method="GET">
              <div class="form-group">
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
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>予約ID</th>
                <th>日付</th>
                <th>ユーザーID</th>
                <th>ユーザー名</th>
                <th>取引種別</th>
                <th>消費ポイント</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach ($sales as $sale)
              <tr>
                <td>{{ $sale->order_id }}</td>
                <td>{{ Carbon\Carbon::parse($sale->created_at)->format('Y年m月d日') }}</td>
                <td>{{ $sale->user_id }}</td>
                <td>{{ $sale->user ? $sale->user->fullname : "" }}</td>
                <td>{{ App\Enums\PointType::getDescription($sale->type) }}</td>
                <td>{{ $sale->point }}</td>
                @if ($sale->order)
                  @if ( $sale->order->type == App\Enums\OrderType::NOMINATION)
                  <td><a href="{{ route('admin.orders.order_nominee', ['order' => $sale->order->id]) }}" class="btn-detail">詳細</a></td>
                  @else
                  <td><a href="{{ route('admin.orders.call', ['order' => $sale->order->id]) }}" class="btn-detail">詳細</a></td>
                  @endif
                @else
                <td><a href="#" class="btn-detail">詳細</a></td>
                @endif
              </tr>
              @endforeach
              <tr>
                <td class="result">合計</td>
                <td class="result">-</td>
                <td class="result">-</td>
                <td class="result">-</td>
                <td class="result">-</td>
                <td class="result">{{  $totalPoint }}</td>
                <td class="result">-</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($sales->total())
              全 {{ $sales->total() }}件中 {{ $sales->firstItem() }}~{{ $sales->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $sales->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
