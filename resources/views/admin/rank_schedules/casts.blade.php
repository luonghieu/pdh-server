@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-rank-schedule')
        <div class="clearfix"></div>
        @include('admin.partials.notification')
        <div class="clearfix"></div>
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.rank_schedules.casts') }}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{ request()->search }}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search from-date" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date ?? ($rankSchedule->from_date ?? '') }}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker to-date" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->to_date ?? ($rankSchedule->to_date ?? '') }}" placeholder="yyyy/mm/dd"/>
              <button type="" class="fa fa-search btn-search"></button>

              <input type="hidden" name="limit" value="{{ request()->limit }}" />
              <input type="hidden" name="class_id" value="{{ request()->class_id }}" />
              <input type="hidden" name="total_order" value="{{ request()->total_order }}" />
              <input type="hidden" name="avg_rate" value="{{ request()->avg_rate }}" />

              <div class="export-csv">
                <input type="hidden" name="is_export" value="1">
                <button type="submit" class="btn btn-info" name="submit" value="export">エクスポート</button>
              </div>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{ route('admin.rank_schedules.casts') }}" id="limit-page" method="GET">
            <div class="form-group">
              <label class="col-md-1 limit-page">表示件数：</label>
              <div class="col-md-1">
                <input type="hidden" name="from_date" value="{{ request()->from_date }}" />
                <input type="hidden" name="to_date" value="{{ request()->to_date }}" />
                <input type="hidden" name="search" value="{{ request()->search }}" />
                <input type="hidden" name="class_id" value="{{ request()->class_id }}" />
                <input type="hidden" name="total_order" value="{{ request()->total_order }}" />
                <input type="hidden" name="avg_rate" value="{{ request()->avg_rate }}" />

                <select id="select-limit" name="limit" class="form-control">
                  @foreach ([10, 20, 50, 100] as $limit)
                    <option value="{{ $limit }}" {{ request()->limit == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="panel-body">
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
                <th>No.</th>
                <th>キャストID</th>
                <th>キャスト名</th>
                <th class="sorting{{ (request()->class_id) ? '_' . request()->class_id: '' }}">
                  <a href="{{ route('admin.rank_schedules.casts',
                    array_merge($request, ['class_id' => (request()->class_id == 'desc') ? 'asc' : 'desc'])
                    ) }}">キャストクラス
                  </a>
                </th>
                <th class="sorting{{ (request()->total_order) ? '_' . request()->total_order: '' }}">
                  <a href="{{ route('admin.rank_schedules.casts',
                    array_merge($request, ['total_order' => (request()->total_order == 'desc') ? 'asc' : 'desc'])
                    ) }}">参加回数
                  </a>
                </th>
                <th class="sorting{{ (request()->avg_rate) ? '_' . request()->avg_rate: '' }}">
                  <a href="{{ route('admin.rank_schedules.casts',
                    array_merge($request, ['avg_rate' => (request()->avg_rate == 'desc') ? 'asc' : 'desc'])
                    ) }}">平均評価
                  </a>
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if (!$casts->count())
              <tr>
                <td colspan="8">{{ trans('messages.results_not_found') }}</td>
              </tr>
              @else
                @foreach ($casts as $key => $cast)
                <tr>
                  <td>{{ $casts->firstItem() + $key }}</td>
                  <td>{{ $cast->id }}</td>
                  <td>{{ $cast->nickname }}</td>
                  <td>{{ $cast->castClass->name }}</td>
                  <td>{{ $cast->orders->count() }}</td>
                  <td>{{ $cast->ratings->avg('score') ?? 0 }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $cast->id]) }}" class="btn btn-info">詳細</a></td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        @if ($casts->count())
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($casts->total())
              全 {{ $casts->total() }}件中 {{ $casts->firstItem() }}~{{ $casts->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $casts->appends(request()->all())->links() }}
          </ul>
        </div>
        @endif
        
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
@section('admin.js')
  <script type="text/javascript">
    var fromDate = $('.from-date').val();
    var toDate = $('.to-date').val();

    $('body').on('click', '.btn-search', function () {
      var fromDate = $('.from-date').val();
      var toDate = $('.to-date').val();

      if (!fromDate && !toDate) {
        $(this).attr('disabled', 'true');
      } else {
        $('.btn-search').removeAttr('disabled');
      }
    });

    $('body').on('change', '.from-date', function () {
      var fromDate = $(this).val();

      if (!fromDate) {
        $('.btn-search').attr('disabled', 'true');
      } else {
        $('.btn-search').removeAttr('disabled');
      }
    });

    $('body').on('change', '.to-date', function () {
      var toDate = $(this).val();

      if (!toDate) {
        $('.btn-search').attr('disabled', 'true');
      } else {
        $('.btn-search').removeAttr('disabled');
      }
    });
  </script>
@endsection
