@extends('layouts.admin')
@section('admin.content')
  <div class="col-md-10 col-sm-11 main ">
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="display-title">
              <p><b>クーポンID:</b>{{$coupon->id}}の利用履歴</p>
            </div>
          </div>
          <div class="panel-body">
            @include('admin.partials.notification')
            <div class="row">
              <div class="col-lg-8">
                <table class="table table-striped table-bordered bootstrap-datatable">
                  <thead>
                  <tr>
                    <th>No.</th>
                    <th>予約ID</th>
                    <th>予約者ID</th>
                    <th>予約発生時刻</th>
                  </tr>
                  </thead>
                  <tbody>
                  @if (empty($historyCoupons->count()))
                    <tr>
                      <td colspan="4">{{ trans('messages.results_not_found') }}</td>
                    </tr>
                  @else
                    @foreach ($historyCoupons as $key => $historyCoupon)
                      <tr>
                        <td>{{ $historyCoupons->firstItem() + $key }}</td>
                        @php
                          if ($historyCoupon->type == App\Enums\OrderType::NOMINATION) {
                            $routeOrder = route('admin.orders.order_nominee', ['order' => $historyCoupon->id]);
                          } else {
                            $routeOrder = route('admin.orders.call', ['order' => $historyCoupon->id]);
                          }
                        @endphp
                        <td><a href="{{$routeOrder}}">{{$historyCoupon->id}}</a></td>
                        <td><a href="{{route('admin.users.show', ['user' => $historyCoupon->user_id])}}">{{$historyCoupon->user_id}}</a></td>
                        <td>{{ Carbon\Carbon::parse($historyCoupon->created_at)->format('Y/m/d H:i') }}</td>
                      </tr>
                    @endforeach
                  @endif
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <div class="dataTables_info" id="DataTables_Table_0_info">
              @if ($historyCoupons->total())
                全 {{ $historyCoupons->total() }}件中 {{ $historyCoupons->firstItem() }}~{{ $historyCoupons->lastItem() }}件を表示しています
              @endif
            </div>
          </div>
          <div class="pagination-outter">
            <ul class="pagination">
              {{ $historyCoupons->appends(request()->all())->links() }}
            </ul>
          </div>
        </div>
      </div>
      <!--/col-->
    </div>
    <!--/row-->
  </div>
@endsection
