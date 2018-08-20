@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @include('admin.partials.menu-tab-transfer')
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.transfers.non_transfers') }}" method="GET">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->from_date }}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{ request()->to_date }}" placeholder="yyyy/mm/dd"/>
              <input type="text"  name="search" class="form-control transfer-search" value="{{ request()->search }}" placeholder="ユーザーID,名前
"/>
              <button type="submit" class="fa fa-search btn btn-search"></button>
              <div class="export-csv">
                <input type="hidden" name="is_transfers" value="1">
                <button type="submit" class="export-btn" name="submit" value="transfers">CSV出力全銀式</button>
              </div>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{ route('admin.transfers.non_transfers') }}" id="limit-page" method="GET">
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
        <form action="{{ route('admin.transfers.change_transfers') }}" method="POST" id="form-transfer">
          {{ csrf_field() }}
          <div class="btn-change-report report">
            <button type="button" class="submit-transfer">
              <p>
                振込済みに変更する
              </p>
            </button>
          </div>
          <div class="panel-body">
            @include('admin.partials.notification')
            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
                <tr>
                  <th></th>
                  <th>予約ID</th>
                  <th>日付</th>
                  <th>ユーザーID</th>
                  <th>ユーザー名</th>
                  <th>振込金額</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              @if ($transfers->count())
                @foreach ($transfers as $transfer)
                <tr>
                  <td class="select-checkbox">
                    <input class="verify-checkboxs"
                      type="checkbox"
                      value="{{ $transfer->id }}"
                      name="transfer_ids[]">
                  </td>
                  <td>{{ $transfer->order_id }}</td>
                  <td>{{ Carbon\Carbon::parse($transfer->created_at)->format('Y年m月d日') }}</td>
                  <td>{{ $transfer->user_id }}</td>
                  <td>{{ $transfer->user->fullname }}</td>
                  <td>￥{{ number_format($transfer->amount) }}</td>
                  @if($transfer->order)
                    @if (App\Enums\OrderType::NOMINATION == $transfer->order->type)
                    <td>
                      <a href="{{ route('admin.orders.order_nominee', ['order' => $transfer->order_id]) }}" class="btn-detail">詳細</a>
                    </td>
                    @else
                    <td>
                      <a href="{{ route('admin.orders.call', ['order' => $transfer->order_id]) }}" class="btn-detail">詳細</a>
                    </td>
                    @endif
                  @else
                    <td>
                      予約が存在しません
                    </td>
                  @endif
                @endforeach
                 <tr>
                  <td></td>
                  <td>合計</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>¥ {{ number_format($transfers->sum('amount')) }}</td>
                  <td></td>
                </tr>
              @else
                <tr>
                  <td colspan="7">{{ trans('messages.transfer_not_found') }}</td>
                </tr>
              @endif
              </tbody>
            </table>
          </div>
        </form>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($transfers->total())
              全 {{ $transfers->total() }}件中 {{ $transfers->firstItem() }}~{{ $transfers->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $transfers->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
