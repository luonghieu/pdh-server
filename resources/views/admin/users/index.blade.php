@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="{{route('admin.users.index')}}" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前" name="search" value="{{request()->search}}">
              <label for="">From date: </label>
              <input type="text" class="form-control date-picker input-search" name="from_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->from_date}}" placeholder="yyyy/mm/dd" />
              <label for="">To date: </label>
              <input type="text" class="form-control date-picker" name="to_date" id="date01" data-date-format="yyyy/mm/dd" value="{{request()->to_date}}" placeholder="yyyy/mm/dd"/>
              <button type="submit" class="fa fa-search btn-search"></button>
            </form>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="panel-body">
          <form class="navbar-form navbar-left form-search" action="{{route('admin.users.index')}}" id="limit-page" method="GET">
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
                <th class="sorting{{ (request()->id) ? '_' . request()->id: '' }}">
                  <a href="{{ route('admin.users.index',
                    array_merge($request, ['id' => (request()->id == 'asc') ? 'desc' : 'asc',])
                    ) }}">ユーザーID
                   </a>
                </th>
                <th>ニックネーム</th>
                <th>年齢</th>
                <th>会員区分</th>
                <th class="sorting{{ (request()->status) ? '_' . request()->status: '' }}">
                  <a href="{{ route('admin.users.index',
                    array_merge($request, ['status' => (request()->status == 'asc') ? 'desc' : 'asc',])
                    ) }}">ステータス
                   </a>
                </th>
                <th class="sorting{{ (request()->last_active_at) ? '_' . request()->last_active_at: '' }}">
                  <a href="{{ route('admin.users.index',
                    array_merge($request, ['last_active_at' => (request()->last_active_at == 'asc') ? 'desc' : 'asc',])
                    ) }}">オンライン
                   </a>
                </th>
                <th>登録日時</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($users->count()))
                <tr>
                  <td colspan="8">{{ trans('messages.user_not_found') }}</td>
                </tr>
              @else
                @foreach ($users as $key => $user)
                <tr>
                  <td>{{ $users->firstItem() +$key }}</td>
                  <td>{{ $user->id }}</td>
                  <td>{{ $user->nickname }}</td>
                  <td>{{ $user->age }}</td>
                  <td>{{ App\Enums\UserType::getDescription($user->type) }}</td>
                  <td>{{ App\Enums\Status::getDescription($user->status) }}</td>
                  @if ($user->is_online == true)
                  <td>オンライン中</td>
                  @else
                  <td>{{ $user->last_active }}</td>
                  @endif
                  <td>{{ Carbon\Carbon::parse($user->created_at)->format('Y/m/d H:i') }}</td>
                  <td><a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="btn-detail">詳細</a></td>
                </tr>
                @endforeach
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
@section('admin.js')
  <script>
    $(function () {
      if (localStorage.getItem('offer')){
        localStorage.removeItem('offer');
      }
    })
  </script>
@stop
