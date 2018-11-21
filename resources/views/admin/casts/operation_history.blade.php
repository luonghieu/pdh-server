@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
         @include('admin.partials.menu-tab-cast', compact('user'))
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.casts.operation_history', ['user' => $user->id])}}" method="GET">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
              <select class="form-control search-point-type" name="search_point_type">
                @foreach ($pointTypes as $key => $pointType)
                  <option value="{{ $key }}" {{ request()->search_point_type == $key ? 'selected' : '' }}>{{ $pointType }}</option>
                @endforeach
              </select>
              <button type="submit" class="fa fa-search btn btn-search" name="submit" value="search"></button>
              <input type="hidden" name="limit" value="{{ request()->limit }}" />
              <input type="hidden" name="page" value="{{ request()->page }}" />
              <div class="export-csv">
                <input type="hidden" name="is_export" value="1">
                <button type="submit" class="export-btn" name="submit" value="export">CSV出力</button>
              </div>
              <input type="hidden" name="limit" value="{{ request()->limit }}" />
            </form>
          </div>
        </div>
        <div class="current_point">
          <p>現在の残高: {{ number_format($user->point) }}<span class="link-change-point"><a href="javascript:void(0)" id="link-change-point" data-user-id="{{ $user->id }}" data-toggle="modal" data-target="#changePoint">ポイントを修正する</a></span></p>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{ route('admin.casts.operation_history', ['user' => $user->id]) }}" id="limit-page" method="GET">
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
                <input type="hidden" name="page" value="{{ request()->page }}" />
              </div>
            </div>
          </form>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>日付</th>
                <th>取引種別</th>
                <th>予約ID</th>
                <th>取得ポイント</th>
                <th>消費ポイント</th>
                <th>残高</th>
                <th>引き落とし額</th>
              </tr>
            </thead>
            <tbody>
              @if (empty($points->count()))
                <tr>
                  <td colspan="10">{{ trans('messages.point_not_found') }}</td>
                </tr>
              @else
                @foreach ($points as $point)
                  <tr>
                    <td>{{ Carbon\Carbon::parse($point->created_at)->format('Y年m月d日') }}</td>
                    <td>{{ App\Enums\PointType::getDescription($point->type) }}</td>
                    <td>{{ ($point->is_receive) ? $point->order->id : '--' }}</td>
                    <td>{{ ($point->is_receive || ($point->is_adjusted && $point->point > 0)) ? number_format($point->point) : '' }}</td>
                    <td>{{ (($point->is_transfer) || ($point->is_adjusted && $point->point < 0)) ? number_format($point->point) : ''}}</td>
                    <td>{{ number_format($point->balance) }}</td>
                    <td>{{ ($point->is_transfer) ? '￥' . number_format(abs($point->point)) : '' }}</td>
                  </tr>
                @endforeach
                <tr>
                  <td class="result">合計</td>
                  <td class="result">-</td>
                  <td class="result">-</td>
                  <td class="result">{{ number_format($sumPointReceive) }}</td>
                  <td class="result">{{ number_format($sumConsumedPoint) }}</td>
                  <td class="result">{{ number_format($sumBalance) }}</td>
                  <td class="result">￥{{ number_format(abs($sumDebitAmount)) }}</td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
        <div class="modal fade" id="changePoint" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                <p>ポイントを修正する</p>
              </div>
              <!-- message js -->
              <div class="has-error text-center">
                <div class="help-block" id="point-alert">
                </div>
              </div>
              <!--  -->
              <form action="{{ route('admin.casts.change_point', ['user' => $user->id]) }}" method="POST" id="change-point-form">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="change-point-input row">
                  <div class="col-sm-offset-1 col-sm-4">
                    <select class="form-control correction-type" id="correction-type" name="correction_type">
                      @foreach ($pointCorrectionTypes as $key => $pointCorrectionType)
                        <option value="{{ $key }}" {{ request()->correction_type == $key ? 'selected' : '' }}>{{ $pointCorrectionType }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-sm-6">
                    <input type="text" name="point" id="point" value=""> P
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-canceled" data-dismiss="modal">キャンセル</button>
                  <button type="submit" class="btn btn-accept">登録する</button>
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
@section('admin.js')
  <script src="/assets/admin/js/changepoint/change_point.js"></script>
@stop

