@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  @include('admin.partials.alert-error', compact('errors'))
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-point')
        <div class="panel-body handling device-height-search">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.points.index') }}" method="GET">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date }}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->to_date }}" placeholder="yyyy/mm/dd"/>
              <select class="form-control search-point-type" name="search_point_type">
                @foreach ($pointTypes as $key => $pointType)
                  <option value="{{ $key }}" {{ request()->search_point_type == $key ? 'selected' : '' }}>{{ $pointType }}</option>
                @endforeach
              </select>
              <button type="submit" class="fa fa-search btn btn-search"></button>
              <div class="export-csv">
                <input type="hidden" name="is_export" value="1">
                <button type="submit" class="export-btn" name="submit" value="export">CSV出力</button>
              </div>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{ route('admin.points.index') }}" id="limit-page" method="GET">
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
                <input type="hidden" name="search_point_type" value="{{ request()->search_point_type }}" />
              </div>
            </div>
          </form>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>購入ID</th>
                <th>日付</th>
                <th>ユーザーID</th>
                <th>ユーザー名</th>
                <th>取引種別</th>
                <th>購入金額</th>
                <th>購入ポイント</th>
              </tr>
            </thead>
            <tbody>
              @if (empty($points->count()))
                <tr>
                  <td colspan="7">{{ trans('messages.point_buy_not_found') }}</td>
                </tr>
              @else
                @foreach ($points as $key => $point)
                  <tr>
                    <td>{{ $point->id }}</td>
                    <td>{{ Carbon\Carbon::parse($point->created_at)->format('Y年m月d日') }}</td>
                    <td>{{ $point->user_id }}</td>
                    <td>{{ $point->user ? $point->user->nickname : '' }}</td>
                    <td>{{ $point->is_direct_transfer ? 'ポイント購入' : App\Enums\PointType::getDescription($point->type) }}</td>
                    @php
                      $amount = '-';
                      if ($point->is_direct_transfer) {
                          $amount = '¥ ' . number_format($point->point * config('common.point_rate'));
                      } else {
                          if ($point->is_adjusted || !$point->payment || $point->is_invite_code) {
                              //
                          } else {
                              $amount = '¥ ' . number_format($point->payment->amount);
                          }
                      }
                    @endphp
                    <td>{{ $amount }}</td>
                    <td>{{ number_format($point->point) }}</td>
                  </tr>
                @endforeach
                <tr>
                  <td>合計</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>¥ {{ number_format($sumAmount) }}</td>
                  <td>{{ number_format($sumPointBuy) }}</td>
                </tr>
              @endif
            </tbody>
          </table>
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
