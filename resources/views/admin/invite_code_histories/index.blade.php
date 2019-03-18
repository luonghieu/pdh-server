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
                <th class="">
                  <a href="">招待者ID
                  </a>
                </th>
                <th class="">
                  <a href="">利用者ID
                   </a>
                </th>
                <th class="">
                  <a href="">招待コード入力日時
                   </a>
                </th>
                <th class="">
                  <a href="">適用対象予約ID
                   </a>
                </th>
                <th class="">
                  <a href="">招待者へのポイント付与
                   </a>
                </th>
                <th>招待者の購入ID</th>
                <th class="">
                  <a href="">利用者へのポイント付与
                   </a>
                </th>
                <th>利用者の購入ID</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($inviteCodeHistories->count()))
                <tr>
                  <td colspan="10">{{ trans('messages.invite_code_history_not_found') }}</td>
                </tr>
              @else
                @foreach ($inviteCodeHistories as $key => $inviteCodeHistory)
                <tr>
                  <td>{{ $inviteCodeHistories->firstItem() + $key }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $inviteCodeHistory->inviteCode->user_id]) }}">{{ $inviteCodeHistory->inviteCode->user_id }}</a></td>
                  <td><a href="{{ route('admin.users.show', ['user' => $inviteCodeHistory->receive_user_id]) }}">{{ $inviteCodeHistory->receive_user_id }}</a></td>
                  <td>{{ Carbon\Carbon::parse($inviteCodeHistory->created_at)->format('Y/m/d H:i') }}</td>
                  @if ($inviteCodeHistory->order->type == App\Enums\OrderType::NOMINATION)
                    <td><a href="{{ route('admin.orders.order_nominee', ['order' => $inviteCodeHistory->order_id]) }}">{{ $inviteCodeHistory->order_id }}</a></td>
                  @else
                    <td><a href="{{ route('admin.orders.call', ['order' => $inviteCodeHistory->order_id]) }}">{{ $inviteCodeHistory->order_id }}</a></td>
                  @endif
                  <td>{{ \App\Enums\InviteCodeHistoryStatus::getDescription($inviteCodeHistory->status) }}</td>
                  @php
                  $pointId = ''; 
                  $pointReceiveId = ''; 
                  foreach($inviteCodeHistory->points as $point)
                    if ($point->user_id == $inviteCodeHistory->inviteCode->user_id) {
                      $pointId = $point->id;
                    } else {
                      $pointReceiveId = $point->id;
                    }
                  @endphp
                  <td>{{ $pointId }}</td>
                  <td>{{ \App\Enums\InviteCodeHistoryStatus::getDescription($inviteCodeHistory->status) }}</td>
                  <td>{{ $pointReceiveId }}</td>
                  <td><a href="{{ route('admin.invite_code_histories.show', ['invite_code_history' => $inviteCodeHistory->id]) }}" class=" btn btn-detail">詳細</a></td>
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($inviteCodeHistories->total())
              全 {{ $inviteCodeHistories->total() }}件中 {{ $inviteCodeHistories->firstItem() }}~{{ $inviteCodeHistories->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $inviteCodeHistories->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
