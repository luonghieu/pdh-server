@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.casts.index') }}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{ request()->search }}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date }}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->to_date }}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn btn-search"></button>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{ route('admin.casts.index') }}" id="limit-page" method="GET">
            <div class="form-group">
              <label class="col-md-1 limit-page">表示件数：</label>
              <div class="col-md-1">
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
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>ユーザーID</th>
                <th>ニックネーム</th>
                <th>年齢</th>
                <th>会員区分</th>
                <th>ステータス</th>
                <th>オンライン</th>
                <th>本日出勤</th>
                <th>キャスト登録日時</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($casts->count()))
                <tr>
                  <td colspan="10">{{ trans('messages.cast_not_found') }}</td>
                </tr>
              @else
                @foreach ($casts as $key => $cast)
                <tr>
                  <td>{{ $casts->firstItem() + $key }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $cast->id]) }}">{{ $cast->id }}</a></td>
                  <td>{{ $cast->nickname }}</td>
                  <td>{{ $cast->age }}</td>
                  <td>{{ App\Enums\UserType::getDescription($cast->type) }}</td>
                  <td>{{ App\Enums\Status::getDescription($cast->status) }}</td>
                  <td>{{ latestOnlineStatus($cast->last_active_at) }}</td>
                  <td>{{ App\Enums\WorkingType::getDescription($cast->working_today) }}</td>
                  <td>{{ Carbon\Carbon::parse($cast->created_at)->format('Y/m/d H:i') }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $cast->id]) }}"><button class="btn btn-default">詳細</button></a></td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
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
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
