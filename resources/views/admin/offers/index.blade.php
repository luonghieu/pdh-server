@extends('layouts.admin')
@section('admin.content')
<div class="col-md-10 col-sm-11 main ">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-body handling">
          <div class="search">
            <form class="navbar-form navbar-left form-search" action="#" method="GET">
              <input type="text" class="form-control input-search" placeholder="ユーザーID,名前,予約ID" name="search" value="{{request()->search}}">
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
          <div class="col-sm-12">
            <div class="pull-right">
              <a href="{{ route('admin.offers.create') }}" class="btn btn-info">新規オファーを作成する</a>
            </div>
          </div>
        </div>
        <div class="panel-body">
          @include('admin.partials.notification')
          <table class="table table-striped table-bordered bootstrap-datatable">
            <thead>
              <tr>
                <th>No.</th>
                <th>予約ID</th>
                <th>キャスト情報</th>
                <th>ギャラ飲み予定開始日時</th>
                <th>ギャラ飲みの時間</th>
                <th>エリア</th>
                <th>合計予定ポイント</th>
                <th>ステータス</th>
                <th>リンク</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @if (empty($offers->count()))
                <tr>
                  <td colspan="10">{{ trans('messages.results_not_found') }}</td>
                </tr>
              @else
                @php
                  $index = 1;
                @endphp
                @foreach ($offers as $key => $offer)
                <tr>
                  <td>{{ (request()->limit ?: 10) * ((request()->page ?: 1) - 1) + $index++ }}</td>
                  @if (App\Enums\OfferStatus::DONE == $offer->status && $offer->order)
                  <td><a href="{{ route('admin.orders.call', $offer->order->id ) }}">{{ $offer->id }}</a></td>
                  @else
                  <td><a href="{{ route('admin.offers.detail', $offer->id ) }}">{{ $offer->id }}</a></td>
                  @endif
                  <td>{{ count($offer->cast_ids) }}名</td>
                  <td>
                    {{ Carbon\Carbon::parse($offer->date)->format('Y/m/d') }}
                    {{ Carbon\Carbon::parse($offer->start_time_from)->format('H:i') }} ~
                    {{ Carbon\Carbon::parse($offer->start_time_to)->format('H:i') }}
                  </td>
                  <td>{{ $offer->duration }}時間</td>
                  <td>{{ getPrefectureName($offer->prefecture_id) }}</td>
                  <td>{{ number_format($offer->temp_point) }}P</td>
                  <td>{{ App\Enums\OfferStatus::getDescription($offer->status) }}</td>
                  <td>
                    {{ ($offer->status == App\Enums\OfferStatus::ACTIVE) ? (env('APP_URL', false) . "/offers/". $offer->id) : '' }}
                  </td>
                  @if (App\Enums\OfferStatus::DONE == $offer->status && $offer->order)
                  <td><a href="{{ route('admin.orders.call', $offer->order->id) }}" class="btn btn-info">詳細</a></td>
                  @else
                  <td><a href="{{ route('admin.offers.detail', $offer->id) }}" class="btn btn-info">詳細</a></td>
                  @endif
                </tr>
                @endforeach
              @endif
            </tbody>
          </table>
        </div>
        <div class="col-lg-12">
          <div class="dataTables_info" id="DataTables_Table_0_info">
            @if ($offers->total())
              全 {{ $offers->total() }}件中 {{ $offers->firstItem() }}~{{ $offers->lastItem() }}件を表示しています
            @endif
          </div>
        </div>
        <div class="pagination-outter">
          <ul class="pagination">
            {{ $offers->appends(request()->all())->links() }}
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
