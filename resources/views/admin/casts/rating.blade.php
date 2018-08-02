@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        @if ($user->is_cast)
          @include('admin.partials.menu-tab-cast',compact('user'))
        @else
          @include('admin.partials.menu-tab',compact('user'))
        @endif
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
                <th>予約ID</th>
                <th>日時</th>
                <th>満足度</th>
                <th>ルックス・身だしなみ</th>
                <th>愛想・気遣い</th>
                <th>コメント</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($ratings as $key => $rating)
              <tr>
                <td>{{ $ratings->firstItem() +$key }}</td>
                <td><a href="{{ route('admin.users.show', ['user' => $rating->user->id]) }}">{{ $rating->user->id }}</a></td>
                <td>{{ $rating->user->nickname }}</td>
                <td><a href="">{{ $rating->order->id }}</a></td>
                <td>{{ Carbon\Carbon::parse($rating->created_at)->format('Y/m/d H:i') }}</td>
                <td>
                  {{ str_repeat('★', $rating->satisfaction) }}
                </td>
                <td>
                  {{ str_repeat('★', $rating->appearance) }}
                </td>
                <td>
                  {{ str_repeat('★', $rating->friendliness) }}
                </td>
                <td>{{ $rating->comment }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($ratings->total())
              全 {{ $ratings->total() }}件中 {{ $ratings->firstItem() }}~{{ $ratings->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $ratings->appends(request()->all())->links() }}
          </ul>
        </div>
      </div>
    </div>
    <!--/col-->
  </div>
  <!--/row-->
</div>
@endsection
