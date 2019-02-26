@extends('layouts.admin')
@section('admin.content')
  <div class="col-md-10 col-sm-11 main ">
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-body handling">
            <div class="search">
              <form class="navbar-form navbar-left form-search" action="{{ route('admin.coupons.index') }}" method="GET">
                <input type="text" class="form-control input-search" placeholder="クーポン名" name="search" value="{{request()->search}}">
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
            <form class="navbar-form navbar-left form-search" action="{{ route('admin.coupons.index') }}" id="limit-page" method="GET">
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
            <div class="col-sm-12">
              <div class="pull-right">
                <a href="{{ route('admin.coupons.create')  }}" class="btn btn-info">新規クーポン作成</a>
              </div>
            </div>
          </div>
          <div class="panel-body">
            @include('admin.partials.notification')
            <table class="table table-striped table-bordered bootstrap-datatable">
              <thead>
              <tr>
                <th>No.</th>
                <th>クーポン名</th>
                <th>対象ゲスト</th>
                <th>利用回数</th>
                <th>クーポン作成日時</th>
                <th></th>
              </tr>
              </thead>
              <tbody>
              @if (empty($coupons->count()))
                <tr>
                  <td colspan="10">{{ trans('messages.results_not_found') }}</td>
                </tr>
              @else
                @php
                  $index = 1;
                @endphp
                @foreach ($coupons as $key => $coupon)
                  <tr>
                    <td>{{ $coupons->firstItem() +$key }}</td>
                    <td>{{ $coupon->name }}</td>
                    <td>
                      @if($coupon->is_filter_after_created_date)
                      登録時から{{ $coupon->filter_after_created_date }}日間以内
                      @endif
                    </td>
                    <td><a href="{{route('admin.coupons.history', ['coupon' => $coupon->id])}}">{{ count($coupon->users) }}</a></td>
                    <td>{{ $coupon->created_at }}</td>
                    <td>
                      <a href="" class="btn btn-info">詳細</a>
                      <a href="{{route('admin.coupons.delete', ['coupon' => $coupon->id])}}" data-method="delete" class="btn btn-info">削除</a>
                    </td>
                  </tr>
                @endforeach
              @endif
              </tbody>
            </table>
          </div>
          <div class="col-lg-12">
            <div class="dataTables_info" id="DataTables_Table_0_info">
              @if ($coupons->total())
                全 {{ $coupons->total() }}件中 {{ $coupons->firstItem() }}~{{ $coupons->lastItem() }}件を表示しています
              @endif
            </div>
          </div>
          <div class="pagination-outter">
            <ul class="pagination">
              {{ $coupons->appends(request()->all())->links() }}
            </ul>
          </div>
        </div>
      </div>
      <!--/col-->
    </div>
    <!--/row-->
  </div>
@endsection
