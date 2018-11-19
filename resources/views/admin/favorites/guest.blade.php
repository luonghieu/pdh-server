@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-favorite')
        <div class="clearfix"></div>
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="#" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{request()->search}}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn-search"></button>
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
                <th>イイネをしたゲストID</th>
                <th>イイネをしたゲスト</th>
                <th>イイネをされたキャストID</th>
                <th>イイネをされたキャスト</th>
                <th>日時</th>
              </tr>
            </thead>
            <tbody>
              @if (empty($favorites->count()))
                <tr>
                  <td colspan="6">{{ trans('messages.results_not_found') }}</td>
                </tr>
              @else
                @foreach ($favorites as $key => $favorite)
                <tr>
                  <td>{{ $favorites->firstItem() + $key }}</td>
                  <td>{{ $favorite->user_id }}</td>
                  <td><a href="{{ route('admin.users.show', $favorite->user_id) }}">{{ $favorite->user->nickname }}</a></td>
                  <td>{{ $favorite->favorited_id }}</td>
                  <td><a href="{{ route('admin.users.show', $favorite->favorited_id) }}">{{ $favorite->favorited->nickname }}</a></td>
                  <td>{{ Carbon\Carbon::parse($favorite->created_at)->format('Y/m/d H:i') }}</td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($favorites->total())
              全 {{ $favorites->total() }}件中 {{ $favorites->firstItem() }}~{{ $favorites->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $favorites->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
