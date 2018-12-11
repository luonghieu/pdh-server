@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-notification-schedules', compact('type'))
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.notification_schedules.index') }}" method="GET">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date }}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->to_date }}" placeholder="yyyy/mm/dd"/>
              <input type="text"  name="search" class="form-control transfer-search" value="{{ request()->search }}" placeholder="タイトル"/>
              <input type="hidden" name="type" value="{{ $type }}" />
              <button type="submit" class="fa fa-search btn btn-search"></button>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="" id="limit-page" method="GET">
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
                <input type="hidden" name="type" value="{{ $type }}" />
              </div>
            </div>
          </form>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <a href="{{ route('admin.notification_schedules.create') }}?type={{ $type }}" class="btn-register">新規作成</a>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>お知らせID</th>
                <th>タイトル</th>
                <th>投稿日時</th>
                <th>ステータス</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($notificationSchedules->count()))
                <tr>
                  <td colspan="5">{{ trans('messages.results_not_found') }}</td>
                </tr>
              @else
                @foreach ($notificationSchedules as $key => $notificationSchedule)
                <tr>
                  <td>{{ $notificationSchedule->id }}</td>
                  <td class="long-text" >
                    <div class="break-all">
                      {{ $notificationSchedule->title }}
                    </div>
                  </td>
                  <td>{{ Carbon\Carbon::parse($notificationSchedule->send_date)->format('Y/m/d H:i') }}</td>
                  <td>{{ \App\Enums\NotificationScheduleStatus::getDescription($notificationSchedule->status) }}</td>
                  <td>
                    <a href="{{ route('admin.notification_schedules.edit', $notificationSchedule->id) }}?type={{ $type }}" class=" btn btn-detail">詳細</a>
                  </td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($notificationSchedules->total())
              全 {{ $notificationSchedules->total() }}件中 {{ $notificationSchedules->firstItem() }}~{{ $notificationSchedules->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $notificationSchedules->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
