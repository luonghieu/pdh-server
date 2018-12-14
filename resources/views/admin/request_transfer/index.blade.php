@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.request_transfer.index') }}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{request()->search}}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn-search"></button>
              <input type="hidden" name="limit" value="{{ request()->limit }}" />
              <input type="hidden" name="transfer_type" value="{{ request()->transfer_type }}" />
              @include('admin.orders.request_sort')
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{route('admin.request_transfer.index')}}" id="limit-page" method="GET">
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
                <input type="hidden" name="transfer_type" value="{{ request()->transfer_type }}" />
              </div>
            </div>
          </form>
        </div>
        @if (!isset(request()->transfer_type))
        <div class="btn-delete-order">
          <a href="{{ route('admin.request_transfer.index', ['transfer_type' => App\Enums\CastTransferStatus::DENIED]) }}"><button>見送りユーザーリスト</button></a>
        </div>
        @endif
        <div class="panel-body">
          @include('admin.partials.notification')
          @php
            $request = [
              'page' => request()->page,
              'limit' => request()->limit,
              'search' => request()->search,
              'from_date' => request()->from_date,
              'to_date' => request()->to_date,
              'transfer_type' => request()->transfer_type,
           ];
          @endphp
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>ユーザーID</th>
                <th class="sorting{{ (request()->nickname) ? '_' . request()->nickname: '' }}">
                  <a href="{{ route('admin.request_transfer.index',
                    array_merge($request, ['nickname' => (request()->nickname == 'asc') ? 'desc' : 'asc',])
                    ) }}">お名前
                   </a>
                </th>
                <th>年齢</th>
                <th>職業</th>
                <th class="sorting{{ (request()->request_transfer_date) ? '_' . request()->request_transfer_date: '' }}">
                  <a href="{{ route('admin.request_transfer.index',
                    array_merge($request, ['request_transfer_date' => (request()->request_transfer_date == 'asc') ? 'desc' : 'asc',])
                    ) }}">申請日時
                   </a>
                </th>
                <th class="column-th-btn"></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($casts->count()))
                <tr>
                  <td colspan="14">{{ trans('messages.results_not_found') }}</td>
                </tr>
              @else
                @foreach ($casts as $key => $cast)
                <tr>
                  <td>{{ $casts->firstItem() + $key }}</td>
                  <td>{{ $cast->id }}</td>
                  <td>{{ $cast->fullname }}</td>
                  <td>{{ $cast->age }}</td>
                  <td>{{ $cast->job ? $cast->job->name : ""}}</td>
                  <td>{{ Carbon\Carbon::parse($cast->request_transfer_date)->format('Y/m/d H:i') }}</td>
                  <td><a href="{{ route('admin.request_transfer.show', ['cast' => $cast->id]) }}" class="btn btn-detail">詳細</a></td>
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
