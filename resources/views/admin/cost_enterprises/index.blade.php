@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="#" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{request()->search}}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn btn-search"></button>

              <input type="hidden" name="limit" value="{{ request()->limit }}" />
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="#" id="limit-page" method="GET">
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
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>購入ID</th>
                <th class="">
                  <a href="">ゲストID
                  </a>
                </th>
                <th class="">
                  <a href="">予約ID
                   </a>
                </th>
                <th class="">
                  <a href="">日時
                   </a>
                </th>
                <th class="">
                  <a href="">種別
                   </a>
                </th>
                <th>増加ポイント</th>
                <th>減少ポイント</th>
              </tr>
            </thead>
            <tbody>
              @if (empty($costEnterprises->count()))
                <tr>
                  <td colspan="8">{{ trans('messages.point_not_found') }}</td>
                </tr>
              @else
                @foreach ($costEnterprises as $key => $costEnterprise)
                <tr>
                  <td>{{ $costEnterprises->firstItem() + $key }}</td>
                  @if (is_array($costEnterprise))
                    <td>{{ $costEnterprise['point_id'] }}</td>
                    <td><a href="{{ route('admin.users.show', ['user' => $costEnterprise['user_id']]) }}">{{ $costEnterprise['user_id'] }}</a></td>
                    @if ($costEnterprise['order_type'] == App\Enums\OrderType::NOMINATION)
                      <td><a href="{{ route('admin.orders.order_nominee', ['order' => $costEnterprise['order_id']]) }}">{{ $costEnterprise['order_id'] }}</a></td>
                    @else
                      <td><a href="{{ route('admin.orders.call', ['order' => $costEnterprise['order_id']]) }}">{{ $costEnterprise['order_id'] }}</a></td>
                    @endif
                    <td>{{ Carbon\Carbon::parse($costEnterprise['created_at'])->format('Y/m/d H:i') }}</td>
                    <td>ポイント消費</td>
                    <td>-</td>
                    <td>{{ $costEnterprise['point'] }}</td> 
                  @else
                    <td>{{ $costEnterprise->id }}</td>
                    <td><a href="{{ route('admin.users.show', ['user' => $costEnterprise->user_id]) }}">{{ $costEnterprise->user_id }}</a></td>
                    @if (!$costEnterprise->order_id)
                    <td>-</td>
                    @else
                      @if ($costEnterprise->order->type == App\Enums\OrderType::NOMINATION)
                        <td><a href="{{ route('admin.orders.order_nominee', ['order' => $costEnterprise->order_id]) }}">{{ $costEnterprise->order_id }}</a></td>
                      @else
                        <td><a href="{{ route('admin.orders.call', ['order' => $costEnterprise->order_id]) }}">{{ $costEnterprise->order_id }}</a></td>
                      @endif
                    @endif
                    <td>{{ Carbon\Carbon::parse($costEnterprise->created_at)->format('Y/m/d H:i') }}</td>
                    @php
                    switch ($costEnterprise->type) {
                        case App\Enums\PointType::INVITE_CODE:
                            $type = 'ポイント付与';
                            $pointIncrease = $costEnterprise->point;
                            $pointDecrease = '-';

                            break;
                        case App\Enums\PointType::EVICT:
                            $type = 'ポイント失効';
                            $pointIncrease = '-';
                            $pointDecrease = $costEnterprise->point;
                            break;
                        
                        default:break;
                    }
                    @endphp
                    <td>{{ $type }}</td>
                    <td>{{ $pointIncrease }}</td>
                    <td>{{ $pointDecrease }}</td>
                  @endif
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($costEnterprises->total())
              全 {{ $costEnterprises->total() }}件中 {{ $costEnterprises->firstItem() }}~{{ $costEnterprises->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $costEnterprises->appends(request()->all())->links() }}
          </ul>
        </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection