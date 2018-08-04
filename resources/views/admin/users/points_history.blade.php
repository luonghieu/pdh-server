@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
         @include('admin.partials.menu-tab',compact('user'))
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.users.points_history', ['user' => $user->id])}}" method="GET">
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
              <div class="current_point">
                <p>現在の残高: {{ number_format($user->point )}}<span class="link-change-point"><a href="javascript:void(0)" data-toggle="modal" data-target="#changePoint">ポイントを修正する</a></span></p>
              </div>
              <div class="export-csv">
                  <input type="hidden" name="is_export" value="1">
                  <button type="submit" class="export-btn" name="submit" value="export">CSV出力</button>
              </div>
            </form>
            <form class="navbar-form navbar-left form-search" action="{{route('admin.users.points_history', ['user' => $user->id])}}" id="limit-page" method="GET">
              <div class="form-group">
                <div class="col-md-1">
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
                <th>日付</th>
                <th>取引タイプ</th>
                <th>購入ID</th>
                <th>予約ID</th>
                <th>請求金額</th>
                <th>購入ポイント</th>
                <th>決済ポイント</th>
                <th>残高</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($points as $point)
              <tr>
                <td>{{ Carbon\Carbon::parse($point->created_at)->format('Y年m月d日') }}</td>
                <td>{{ App\Enums\PointType::getDescription($point->type) }}</td>
                @if ($point->is_buy)
                  <td>{{ $point->id }}</td>
                @else
                  <td>-</td>
                @endif
                @if ($point->is_pay)
                  @php
                    if(($point->order->type == App\Enums\OrderType::NOMINATED_CALL)|| ($point->order->type == App\Enums\OrderType::CALL)) {
                      $link = route('admin.orders.call', ['order' => $point->order->id]);
                    } else {
                      $link = "#";
                    }
                  @endphp
                  <td><a href="{{ $link }}">{{ $point->order->id }}</a></td>
                @else
                  <td>-</td>
                @endif
                @if ($point->is_adjusted)
                <td>-</td>
                @else
                <td>￥{{ $point->payment->amount }}</td>
                @endif
                @if ($point->is_buy)
                <td>{{ $point->point }}</td>
                @else
                <td></td>
                @endif
                @if ($point->is_pay)
                <td>{{ $point->point }}</td>
                @else
                <td></td>
                @endif
                <td>{{ $point->balance }}</td>
              </tr>
              @endforeach
              <tr>
                <td class="result">合計</td>
                <td class="result">-</td>
                <td class="result">-</td>
                <td class="result">-</td>
                <td class="result">{{ $sumAmount }}</td>
                <td class="result">{{  $sumPointBuy }}</td>
                <td class="result">{{ $sumPointPay }}</td>
                <td class="result">{{ $sumBalance }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal fade" id="changePoint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>ポイントを修正する</p>
              </div>
              <form action="{{ route('admin.users.change_point', ['user' => $user->id]) }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="change-point-input">
                 <input type="text" name="point"> P
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept">はい</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($points->total())
              全 {{ $points->total() }}件中 {{ $points->firstItem() }}~{{ $points->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $points->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
