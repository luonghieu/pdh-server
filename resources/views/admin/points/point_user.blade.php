@extends('layouts.admin')
@section('admin.content')

<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-point')
        <div class="panel-body handling device-height-search">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.points.point_users') }}" method="GET">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date }}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->to_date }}" placeholder="yyyy/mm/dd"/>
              <select class="form-control search-point-type" name="user_type">
                @foreach ($userTypes as $key => $userType)
                  <option value="{{ $key }}" {{ request()->user_type == $key ? 'selected' : '' }}>{{ $userType }}</option>
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
          <form class="navbar-form navbar-left form-search" action="{{ route('admin.points.point_users') }}" id="limit-page" method="GET">
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
                <input type="hidden" name="user_type" value="{{ request()->user_type }}" />
              </div>
            </div>
          </form>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>ユーザーID</th>
                <th>ユーザー名</th>
                <th>ユーザー種別</th>
                <th>ポイントの増加額</th>
                <th>ポイントの減少額</th>
                <th>ポイントの残高</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($users->count()))
                <tr>
                  <td colspan="10">{{ trans('messages.user_not_found') }}</td>
                </tr>
              @else
                @php
                  $totalPositivePoints =0;
                  $totalNegativePoints =0;
                  $totalBalance =0;
                  $admin = null;
                @endphp
                @foreach ($users as $key => $user)
                  @if (!$user->is_admin)
                  <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->fullname }}</td>
                    <td>{{ $userTypes[$user->type] }}</td>
                    <td>{{ number_format($user->positivePoints($user->points)) }}</td>
                    <td>{{ number_format($user->negativePoints($user->points)) }}</td>
                    <td>{{ number_format($user->totalBalance($user->points)) }}</td>
                    <td>
                      <a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="btn btn-detail">詳細</a>
                    </td>
                  </tr>
                  @php
                    $totalPositivePoints +=$user->positivePoints($user->points);
                    $totalNegativePoints +=$user->negativePoints($user->points);
                    $totalBalance +=$user->totalBalance($user->points);
                  @endphp
                  @else
                    @php
                    $admin = $user;
                    @endphp
                  @endif
                @endforeach
                  @if ($admin)
                  <tr>
                    <td>管理者</td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($admin->positivePoints($admin->points)) }}</td>
                    <td>{{ number_format($admin->negativePoints($admin->points)) }}</td>
                    <td>{{ number_format($admin->totalBalance($admin->points)) }}</td>
                    <td></td>
                  </tr>
                  @php
                    $totalPositivePoints +=$admin->positivePoints($admin->points);
                    $totalNegativePoints +=$admin->negativePoints($admin->points);
                    $totalBalance +=$admin->totalBalance($admin->points);
                  @endphp
                  @endif
                <tr>
                  <td>合計</td>
                  <td></td>
                  <td></td>
                  <td>{{ number_format($totalPositivePoints) }}</td>
                  <td>{{ number_format($totalNegativePoints) }}</td>
                  <td>{{ number_format($totalBalance) }}</td>
                  <td></td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($users->total())
              全 {{ $users->total() }}件中 {{ $users->firstItem() }}~{{ $users->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $users->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
